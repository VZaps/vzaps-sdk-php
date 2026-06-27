<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Common;

final class InstanceCreateRequest implements VZapsModel
{
    /**
     * @param string|list<string>|null $eventsSubscribe
     * @param array<string, mixed> $extra
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $webhook = null,
        public readonly string|array|null $eventsSubscribe = null,
        public readonly array $extra = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter(array_replace([
            'name' => $this->name,
            'webhook' => $this->webhook,
            'eventsSubscribe' => $this->eventsSubscribe,
        ], $this->extra), static fn (mixed $value): bool => $value !== null);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
