<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Common;

final class GenericInstanceRequest extends InstanceScopedRequest
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        string $instanceId,
        string $instanceToken,
        public readonly array $data = [],
    ) {
        parent::__construct($instanceId, $instanceToken);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_replace($this->scope(), $this->data);
    }
}
