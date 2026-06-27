<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Common;

interface VZapsModel extends \JsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
