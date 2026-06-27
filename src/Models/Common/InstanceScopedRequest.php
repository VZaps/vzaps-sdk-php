<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Common;

abstract class InstanceScopedRequest implements VZapsModel
{
    public function __construct(
        public readonly string $instanceId,
        public readonly string $instanceToken,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function scope(): array
    {
        return [
            'instanceId' => $this->instanceId,
            'instanceToken' => $this->instanceToken,
        ];
    }
}
