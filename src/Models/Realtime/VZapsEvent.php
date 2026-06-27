<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Realtime;

final class VZapsEvent
{
    /**
     * @param mixed $data
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $instanceId,
        public readonly string $createdAt,
        public readonly mixed $data,
        public readonly array $raw,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            id: (string) ($payload['id'] ?? ''),
            type: (string) ($payload['type'] ?? ''),
            instanceId: (string) ($payload['instance_id'] ?? $payload['instanceId'] ?? ''),
            createdAt: (string) ($payload['created_at'] ?? $payload['createdAt'] ?? ''),
            data: $payload['data'] ?? null,
            raw: $payload,
        );
    }
}
