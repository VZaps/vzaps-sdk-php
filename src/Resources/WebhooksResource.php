<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class WebhooksResource extends BaseResource
{
    public function get(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/webhook', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function set(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/webhook', $request);
    }

    public function searchLogs(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/webhook/logs/search', $request);
    }

    public function getLog(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $logId = $body['logId'] ?? $body['log_id'] ?? null;
        if (!is_scalar($logId) || trim((string) $logId) === '') {
            throw new \InvalidArgumentException('logId is required.');
        }

        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/webhook/logs/' . $this->esc((string) $logId), options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function retryLog(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $logId = $body['logId'] ?? $body['log_id'] ?? null;
        if (!is_scalar($logId) || trim((string) $logId) === '') {
            throw new \InvalidArgumentException('logId is required.');
        }

        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/webhook/logs/' . $this->esc((string) $logId) . '/retry', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
