<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class ContactsResource extends BaseResource
{
    public function list(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/contact/list', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function add(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/contact/add', $request);
    }
}
