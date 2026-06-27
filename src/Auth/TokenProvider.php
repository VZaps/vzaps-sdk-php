<?php

declare(strict_types=1);

namespace VZaps\Sdk\Auth;

use VZaps\Sdk\Exceptions\VZapsAuthenticationException;
use VZaps\Sdk\Http\VZapsHttpClient;
use VZaps\Sdk\Http\VZapsHttpRequest;
use VZaps\Sdk\VZapsClientOptions;

final class TokenProvider
{
    private ?string $accessToken = null;
    private int $expiresAt = 0;

    public function __construct(
        private readonly VZapsHttpClient $http,
        private readonly VZapsClientOptions $options,
    ) {
    }

    public function getAccessToken(): string
    {
        if ($this->accessToken !== null && $this->expiresAt > time()) {
            return $this->accessToken;
        }

        return $this->refresh();
    }

    public function forceRefresh(): string
    {
        $this->accessToken = null;
        $this->expiresAt = 0;

        return $this->refresh();
    }

    private function refresh(): string
    {
        $response = $this->http->sendRaw(new VZapsHttpRequest(
            method: 'POST',
            path: '/token',
            body: [
                'clientToken' => $this->options->clientToken,
                'clientSecret' => $this->options->clientSecret,
            ],
            auth: false,
        ));

        if (!is_array($response->data)) {
            throw new VZapsAuthenticationException(
                'VZaps token response is not a JSON object.',
                $response->statusCode,
                responseBody: $response->rawBody,
            );
        }

        $accessToken = $response->data['access_token'] ?? $response->data['accessToken'] ?? null;
        $expiresIn = $response->data['expires_in'] ?? $response->data['expiresIn'] ?? null;

        if (!is_string($accessToken) || trim($accessToken) === '' || !is_numeric($expiresIn)) {
            throw new VZapsAuthenticationException(
                'VZaps token response is missing access_token or expires_in.',
                $response->statusCode,
                details: $response->data,
                responseBody: $response->rawBody,
            );
        }

        $this->accessToken = $accessToken;
        $this->expiresAt = time() + max(0, (int) $expiresIn - $this->options->tokenSkewSeconds);

        return $this->accessToken;
    }
}
