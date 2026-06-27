<?php

declare(strict_types=1);

namespace VZaps\Sdk\Http;

final class VZapsHttpRequest
{
    /**
     * @param array<string, string> $headers
     * @param array<string, scalar|null> $query
     * @param mixed $body
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly mixed $body = null,
        public readonly array $query = [],
        public readonly array $headers = [],
        public readonly ?string $instanceToken = null,
        public readonly bool $auth = true,
    ) {
    }
}
