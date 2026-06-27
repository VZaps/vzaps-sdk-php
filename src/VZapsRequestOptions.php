<?php

declare(strict_types=1);

namespace VZaps\Sdk;

final class VZapsRequestOptions
{
    /**
     * @param array<string, string> $headers
     * @param array<string, scalar|null> $query
     */
    public function __construct(
        public readonly ?string $instanceToken = null,
        public readonly array $headers = [],
        public readonly array $query = [],
        public readonly bool $auth = true,
    ) {
    }
}
