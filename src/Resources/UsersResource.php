<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class UsersResource extends BaseResource
{
    public function info(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/user/info', $request);
    }

    public function check(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/user/check', $request);
    }

    public function avatar(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/user/avatar', $request);
    }

    public function contacts(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/user/contacts', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
