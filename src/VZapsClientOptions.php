<?php

declare(strict_types=1);

namespace VZaps\Sdk;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

final class VZapsClientOptions
{
    public const DEFAULT_BASE_URL = 'https://api.vzaps.com';
    public const DEFAULT_REALTIME_URL = 'wss://realtime.vzaps.com';

    public readonly string $clientToken;
    public readonly string $clientSecret;
    public readonly string $baseUrl;
    public readonly string $realtimeUrl;
    public readonly float $timeoutSeconds;
    public readonly int $tokenSkewSeconds;
    public readonly string $userAgent;
    public readonly ClientInterface $httpClient;
    public readonly ?\Closure $webSocketFactory;

    public function __construct(
        string $clientToken,
        string $clientSecret,
        ?string $baseUrl = null,
        ?string $realtimeUrl = null,
        float $timeoutSeconds = 30.0,
        int $tokenSkewSeconds = 60,
        ?string $userAgent = null,
        ?ClientInterface $httpClient = null,
        ?callable $webSocketFactory = null,
    ) {
        $this->clientToken = self::requireNonEmpty($clientToken, 'clientToken');
        $this->clientSecret = self::requireNonEmpty($clientSecret, 'clientSecret');
        $this->baseUrl = self::normalizeBaseUrl($baseUrl ?? self::DEFAULT_BASE_URL);
        $this->realtimeUrl = self::normalizeBaseUrl($realtimeUrl ?? self::DEFAULT_REALTIME_URL);
        $this->timeoutSeconds = $timeoutSeconds;
        $this->tokenSkewSeconds = max(0, $tokenSkewSeconds);
        $this->userAgent = $userAgent ?? 'VZaps.SDK.PHP/0.1.0';
        $this->httpClient = $httpClient ?? new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
            'timeout' => $timeoutSeconds,
        ]);
        $this->webSocketFactory = $webSocketFactory === null ? null : \Closure::fromCallable($webSocketFactory);
    }

    private static function requireNonEmpty(string $value, string $name): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new Exceptions\VZapsException(sprintf('VZaps %s is required.', $name));
        }

        return $trimmed;
    }

    private static function normalizeBaseUrl(string $value): string
    {
        return rtrim($value, '/');
    }
}
