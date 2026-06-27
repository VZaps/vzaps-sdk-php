<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class SessionsResource extends BaseResource
{
    public function status(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/session/status', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function qr(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/session/qr', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function pairCode(string $instanceId, string $phone, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/session/paircode/' . $this->esc($phone), options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function disconnect(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/session/disconnect', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
