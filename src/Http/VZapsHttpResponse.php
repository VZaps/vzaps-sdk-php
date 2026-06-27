<?php

declare(strict_types=1);

namespace VZaps\Sdk\Http;

final class VZapsHttpResponse
{
    /**
     * @param array<string, list<string>> $headers
     * @param mixed $data
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly mixed $data,
        public readonly string $rawBody,
    ) {
    }
}
