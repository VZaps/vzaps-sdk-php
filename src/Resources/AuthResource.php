<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

final class AuthResource extends BaseResource
{
    public function getAccessToken(): string
    {
        return $this->http->getAccessToken();
    }
}
