<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Common;

final class InstanceGetRequest implements VZapsModel
{
    public function __construct(public readonly string $id)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['id' => $this->id];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
