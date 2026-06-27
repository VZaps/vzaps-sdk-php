<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Common;

final class InstanceRequestOptions
{
    public function __construct(public readonly ?string $instanceToken = null)
    {
    }
}
