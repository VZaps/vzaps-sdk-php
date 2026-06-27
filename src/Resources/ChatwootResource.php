<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class ChatwootResource extends BaseResource
{
    public function get(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/chatwoot', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function set(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/chatwoot', $request);
    }

    public function delete(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('DELETE', '/instances/' . $this->esc($instanceId) . '/chatwoot', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function triggerImport(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $what = $body['what'] ?? null;
        if (!is_scalar($what) || trim((string) $what) === '') {
            throw new \InvalidArgumentException('what is required.');
        }

        unset($body['what']);

        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/chatwoot/import/' . $this->esc((string) $what), $this->bodyOrNull($body), new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
