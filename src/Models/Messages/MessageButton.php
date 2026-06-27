<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Messages;

use VZaps\Sdk\Models\Common\VZapsModel;

final class MessageButton implements VZapsModel
{
    /**
     * @param array<string, mixed> $extra
     */
    public function __construct(
        public readonly string $id,
        public readonly string $text,
        public readonly array $extra = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_replace(['id' => $this->id, 'text' => $this->text], $this->extra);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
