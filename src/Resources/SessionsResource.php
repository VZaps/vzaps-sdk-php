<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\Models\Sessions\SessionStatusData;
use VZaps\Sdk\Models\Sessions\SessionStatusResponse;
use VZaps\Sdk\VZapsRequestOptions;

final class SessionsResource extends BaseResource
{
    public function status(string $instanceId, ?string $instanceToken = null): SessionStatusResponse
    {
        $payload = $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/session/status', options: new VZapsRequestOptions(instanceToken: $instanceToken));

        if (!is_array($payload)) {
            return new SessionStatusResponse(0, false, new SessionStatusData(false));
        }

        return SessionStatusResponse::fromArray($payload);
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
