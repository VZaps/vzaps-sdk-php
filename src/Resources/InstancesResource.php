<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\Models\Common\InstanceCreateRequest;
use VZaps\Sdk\Models\Common\InstanceListRequest;
use VZaps\Sdk\VZapsRequestOptions;

final class InstancesResource extends BaseResource
{
    /**
     * @param InstanceCreateRequest|array<string, mixed> $request
     */
    public function create(InstanceCreateRequest|array $request): mixed
    {
        return $this->sendRequest('PUT', '/instances/create', $request);
    }

    /**
     * @param InstanceListRequest|array<string, mixed>|null $request
     */
    public function list(InstanceListRequest|array|null $request = null): mixed
    {
        $body = $request instanceof InstanceListRequest ? $request->toArray() : $this->toArray($request);
        $body = array_replace([
            'page' => 1,
            'size' => $body['pageSize'] ?? $body['page_size'] ?? 20,
            'filter' => [],
        ], $body);

        if (isset($body['search']) && is_string($body['search']) && trim($body['search']) !== '') {
            $body['filter'] = array_replace(is_array($body['filter']) ? $body['filter'] : [], ['query' => trim($body['search'])]);
        }

        unset($body['search'], $body['pageSize'], $body['page_size']);

        return $this->sendRequest('POST', '/instances/list', $body);
    }

    public function get(string $instanceId): mixed
    {
        return $this->sendRequest('POST', '/instances/get', ['id' => $instanceId]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(string $instanceId, array $data, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('PATCH', '/instances/' . $this->esc($instanceId), $data, new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function restart(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/restart', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function delete(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('DELETE', '/instances/' . $this->esc($instanceId), options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    /**
     * @param InstanceCreateRequest|array<string, mixed> $request
     */
    public function provision(InstanceCreateRequest|array $request): mixed
    {
        return $this->sendRequest('PUT', '/instances/provision', $request);
    }

    /**
     * @param array<string, mixed> $request
     */
    public function search(array $request): mixed
    {
        return $this->sendRequest('POST', '/instances/search', $request);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function subscribe(string $instanceId, array $data = [], ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/subscribe', $data, new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function resumeSubscription(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/resume-subscription', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function cancel(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('PUT', '/instances/' . $this->esc($instanceId) . '/cancel', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
