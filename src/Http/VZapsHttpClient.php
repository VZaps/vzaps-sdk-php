<?php

declare(strict_types=1);

namespace VZaps\Sdk\Http;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use VZaps\Sdk\Auth\TokenProvider;
use VZaps\Sdk\Exceptions\VZapsApiException;
use VZaps\Sdk\Exceptions\VZapsAuthenticationException;
use VZaps\Sdk\Exceptions\VZapsException;
use VZaps\Sdk\Exceptions\VZapsRateLimitException;
use VZaps\Sdk\Exceptions\VZapsTimeoutException;
use VZaps\Sdk\Json\VZapsJson;
use VZaps\Sdk\VZapsClientOptions;
use VZaps\Sdk\VZapsRequestOptions;

final class VZapsHttpClient
{
    private TokenProvider $tokenProvider;

    public function __construct(private readonly VZapsClientOptions $options)
    {
        $this->tokenProvider = new TokenProvider($this, $options);
    }

    public function getAccessToken(): string
    {
        return $this->tokenProvider->getAccessToken();
    }

    /**
     * @param mixed $body
     * @return mixed
     */
    public function request(string $method, string $path, mixed $body = null, ?VZapsRequestOptions $options = null): mixed
    {
        $options ??= new VZapsRequestOptions();

        try {
            return $this->send(new VZapsHttpRequest(
                method: $method,
                path: $path,
                body: $body,
                query: $options->query,
                headers: $options->headers,
                instanceToken: $options->instanceToken,
                auth: $options->auth,
            ), retryOnUnauthorized: true)->data;
        } catch (VZapsAuthenticationException $exception) {
            if ($exception->statusCode !== 401 || !$options->auth) {
                throw $exception;
            }

            $this->tokenProvider->forceRefresh();

            return $this->send(new VZapsHttpRequest(
                method: $method,
                path: $path,
                body: $body,
                query: $options->query,
                headers: $options->headers,
                instanceToken: $options->instanceToken,
                auth: $options->auth,
            ), retryOnUnauthorized: false)->data;
        }
    }

    public function sendRaw(VZapsHttpRequest $request): VZapsHttpResponse
    {
        return $this->send($request, retryOnUnauthorized: false);
    }

    /**
     * @param array<string, scalar|null> $query
     */
    public function buildUrl(string $path, array $query = []): string
    {
        $cleanPath = '/' . ltrim($path, '/');
        $url = $this->options->baseUrl . $cleanPath;
        $normalizedQuery = [];

        foreach ($query as $key => $value) {
            if ($value !== null) {
                $normalizedQuery[VZapsJson::toSnakeCase((string) $key)] = $value;
            }
        }

        $queryString = http_build_query($normalizedQuery, '', '&', PHP_QUERY_RFC3986);

        return $queryString === '' ? $url : $url . '?' . $queryString;
    }

    /**
     * @param array<string, scalar|null> $query
     */
    public function buildRealtimeUrl(string $path, array $query = []): string
    {
        $cleanPath = '/' . ltrim($path, '/');
        $url = $this->options->realtimeUrl . $cleanPath;
        $normalizedQuery = [];

        foreach ($query as $key => $value) {
            if ($value !== null) {
                $normalizedQuery[VZapsJson::toSnakeCase((string) $key)] = $value;
            }
        }

        $queryString = http_build_query($normalizedQuery, '', '&', PHP_QUERY_RFC3986);

        return $queryString === '' ? $url : $url . '?' . $queryString;
    }

    /**
     * @param array<string, string> $extraHeaders
     * @return array<string, string>
     */
    public function authHeaders(?string $instanceToken = null, array $extraHeaders = []): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->tokenProvider->getAccessToken(),
            'X-Client-Token' => $this->options->clientToken,
        ];

        if ($instanceToken !== null && $instanceToken !== '') {
            $headers['X-Instance-Token'] = $instanceToken;
        }

        return array_replace($headers, $extraHeaders);
    }

    private function send(VZapsHttpRequest $request, bool $retryOnUnauthorized): VZapsHttpResponse
    {
        $headers = [
            'Accept' => 'application/json',
            'User-Agent' => $this->options->userAgent,
        ];

        $body = null;
        if ($request->body !== null) {
            $headers['Content-Type'] = 'application/json';
            $body = VZapsJson::encode($request->body);
        }

        if ($request->auth) {
            $headers = array_replace($headers, $this->authHeaders($request->instanceToken));
        } elseif ($request->instanceToken !== null && $request->instanceToken !== '') {
            $headers['X-Instance-Token'] = $request->instanceToken;
        }

        $headers = array_replace($headers, $request->headers);
        $psrRequest = new Request($request->method, $this->buildUrl($request->path, $request->query), $headers, $body);

        try {
            $response = $this->options->httpClient->sendRequest($psrRequest);
        } catch (ConnectException $exception) {
            throw new VZapsTimeoutException('VZaps request timed out.', previous: $exception);
        } catch (ClientExceptionInterface $exception) {
            throw new VZapsException('VZaps HTTP client failed: ' . $this->redact($exception->getMessage()), previous: $exception);
        } catch (Throwable $exception) {
            if (str_contains(strtolower($exception->getMessage()), 'timed out')) {
                throw new VZapsTimeoutException('VZaps request timed out.', previous: $exception);
            }

            throw $exception;
        }

        $parsed = $this->parseResponse($response);

        if ($parsed->statusCode === 401 && $retryOnUnauthorized && $request->auth) {
            throw new VZapsAuthenticationException(
                'VZaps authentication failed.',
                401,
                responseBody: $this->redact($parsed->rawBody),
            );
        }

        return $parsed;
    }

    private function parseResponse(ResponseInterface $response): VZapsHttpResponse
    {
        $statusCode = $response->getStatusCode();
        $rawBody = (string) $response->getBody();
        $data = null;

        if (trim($rawBody) !== '') {
            try {
                $data = VZapsJson::decode($rawBody);
            } catch (JsonException) {
                $data = $rawBody;
            }
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $this->throwForStatus($statusCode, $data, $rawBody, $response);
        }

        return new VZapsHttpResponse($statusCode, $this->normalizeHeaders($response->getHeaders()), $data, $rawBody);
    }

    /**
     * @param array<string, array<int, string>> $headers
     *
     * @return array<string, list<string>>
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $name => $values) {
            $normalized[$name] = array_values($values);
        }

        return $normalized;
    }

    private function throwForStatus(int $statusCode, mixed $data, string $rawBody, ResponseInterface $response): never
    {
        $message = $this->readErrorMessage($data, $response->getReasonPhrase());
        $errorCode = is_array($data) && isset($data['code']) && is_scalar($data['code']) ? (string) $data['code'] : null;
        $requestId = $response->getHeaderLine('X-Request-Id') ?: null;
        $responseBody = $this->truncate($this->redact($rawBody));

        if ($statusCode === 401 || $statusCode === 403) {
            throw new VZapsAuthenticationException($message, $statusCode, $errorCode, $data, $requestId, $responseBody);
        }

        if ($statusCode === 408) {
            throw new VZapsTimeoutException($message);
        }

        if ($statusCode === 429) {
            throw new VZapsRateLimitException($message, $statusCode, $errorCode, $data, $requestId, $responseBody);
        }

        throw new VZapsApiException($message, $statusCode, $errorCode, $data, $requestId, $responseBody);
    }

    private function readErrorMessage(mixed $data, string $fallback): string
    {
        if (is_array($data)) {
            foreach (['message', 'error', 'detail'] as $key) {
                if (isset($data[$key]) && is_string($data[$key]) && trim($data[$key]) !== '') {
                    return $this->redact($data[$key]);
                }
            }
        }

        return $fallback !== '' ? $fallback : 'VZaps request failed.';
    }

    private function truncate(string $value, int $maxLength = 4096): string
    {
        if (strlen($value) <= $maxLength) {
            return $value;
        }

        return substr($value, 0, $maxLength) . '...';
    }

    private function redact(string $value): string
    {
        return str_replace(
            [$this->options->clientToken, $this->options->clientSecret],
            ['[REDACTED_CLIENT_TOKEN]', '[REDACTED_CLIENT_SECRET]'],
            $value,
        );
    }
}
