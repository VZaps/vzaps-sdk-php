<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class GroupsResource extends BaseResource
{
    public function list(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);

        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/group/list', options: new VZapsRequestOptions(
            instanceToken: $instanceToken,
            query: [
                'page' => $body['page'] ?? null,
                'pageSize' => $body['pageSize'] ?? $body['page_size'] ?? null,
            ],
        ));
    }

    public function get(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);

        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/group/info', options: new VZapsRequestOptions(
            instanceToken: $instanceToken,
            query: ['groupId' => $body['groupId'] ?? $body['group_id'] ?? null],
        ));
    }

    public function inviteLink(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);

        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/group/invitelink', options: new VZapsRequestOptions(
            instanceToken: $instanceToken,
            query: [
                'groupId' => $body['groupId'] ?? $body['group_id'] ?? null,
                'reset' => $body['reset'] ?? null,
            ],
        ));
    }

    public function setPhoto(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/group/photo', $request);
    }

    public function setName(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/group/name', $request);
    }

    public function setDescription(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/group/description', $request);
    }

    public function setSettings(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/group/settings', $request);
    }

    public function create(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/group/create', $request);
    }

    public function addAdmin(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/group/add-admin', $request);
    }

    public function removeAdmin(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/group/remove-admin', $request);
    }
}
