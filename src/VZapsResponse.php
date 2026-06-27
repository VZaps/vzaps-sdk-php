<?php

declare(strict_types=1);

namespace VZaps\Sdk;

final class VZapsResponse
{
    /**
     * @param array<string, list<string>> $headers
     * @param mixed $data
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly mixed $data,
    ) {
    }
}
