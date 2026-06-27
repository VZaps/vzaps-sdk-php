<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class TypeBotsResource extends BaseResource
{
    public function list(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/typebots', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function create(mixed $request): mixed
    {
        return $this->instanceRequest('POST', '/typebots', $request);
    }

    public function update(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $typebotId = $body['typebotId'] ?? $body['typebot_id'] ?? null;
        if (!is_scalar($typebotId) || trim((string) $typebotId) === '') {
            throw new \InvalidArgumentException('typebotId is required.');
        }

        unset($body['typebotId'], $body['typebot_id']);

        return $this->sendRequest('PATCH', '/instances/' . $this->esc($instanceId) . '/typebots/' . $this->esc((string) $typebotId), $this->bodyOrNull($body), new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function delete(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $typebotId = $body['typebotId'] ?? $body['typebot_id'] ?? null;
        if (!is_scalar($typebotId) || trim((string) $typebotId) === '') {
            throw new \InvalidArgumentException('typebotId is required.');
        }

        return $this->sendRequest('DELETE', '/instances/' . $this->esc($instanceId) . '/typebots/' . $this->esc((string) $typebotId), options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function startSession(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $typebotId = $body['typebotId'] ?? $body['typebot_id'] ?? null;
        unset($body['typebotId'], $body['typebot_id']);

        $path = $typebotId === null || $typebotId === ''
            ? '/typebots/sessions/start'
            : '/typebots/' . $this->esc((string) $typebotId) . '/sessions/start';

        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . $path, $this->bodyOrNull($body), new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function listSessions(string $instanceId, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/typebots/sessions', options: new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function pauseSession(mixed $request): mixed
    {
        return $this->sessionAction($request, '/pause');
    }

    public function closeSession(mixed $request): mixed
    {
        return $this->sessionAction($request, '/close');
    }

    private function sessionAction(mixed $request, string $suffix): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $session = $body['session'] ?? null;
        if (!is_scalar($session) || trim((string) $session) === '') {
            throw new \InvalidArgumentException('session is required.');
        }

        unset($body['session']);

        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/typebots/sessions/' . $this->esc((string) $session) . $suffix, $this->bodyOrNull($body), new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
