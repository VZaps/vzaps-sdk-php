<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\VZapsRequestOptions;

final class ChatsResource extends BaseResource
{
    public function list(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);

        return $this->sendRequest('GET', '/instances/' . $this->esc($instanceId) . '/chats', options: new VZapsRequestOptions(
            instanceToken: $instanceToken,
            query: [
                'page' => $body['page'] ?? null,
                'pageSize' => $body['pageSize'] ?? $body['page_size'] ?? null,
            ],
        ));
    }

    public function get(mixed $request): mixed
    {
        return $this->chatAction('GET', $request, '');
    }

    public function archive(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/archive');
    }

    public function unarchive(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/unarchive');
    }

    public function mute(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/mute');
    }

    public function unmute(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/unmute');
    }

    public function pin(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/pin');
    }

    public function unpin(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/unpin');
    }

    public function read(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/read');
    }

    public function unread(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/unread');
    }

    public function clear(mixed $request): mixed
    {
        return $this->chatAction('POST', $request, '/clear');
    }

    public function delete(mixed $request): mixed
    {
        return $this->chatAction('DELETE', $request, '');
    }

    public function setExpiration(mixed $request): mixed
    {
        return $this->chatAction('PUT', $request, '/expiration');
    }

    private function chatAction(string $method, mixed $request, string $suffix): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $phone = $body['phone'] ?? null;
        if (!is_scalar($phone) || trim((string) $phone) === '') {
            throw new \InvalidArgumentException('phone is required.');
        }

        unset($body['phone']);

        return $this->sendRequest($method, '/instances/' . $this->esc($instanceId) . '/chats/' . $this->esc((string) $phone) . $suffix, $this->bodyOrNull($body), new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
