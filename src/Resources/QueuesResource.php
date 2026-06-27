<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

final class QueuesResource extends BaseResource
{
    public function listMessages(mixed $request): mixed
    {
        return $this->instanceRequest('GET', '/queue/messages', $request);
    }

    public function removeMessage(mixed $request): mixed
    {
        return $this->removeQueueItem($request, '/queue/messages/');
    }

    public function purgeMessages(mixed $request): mixed
    {
        return $this->instanceRequest('DELETE', '/queue/messages', $request);
    }

    public function listOperations(mixed $request): mixed
    {
        return $this->instanceRequest('GET', '/queue/operations', $request);
    }

    public function removeOperation(mixed $request): mixed
    {
        return $this->removeQueueItem($request, '/queue/operations/');
    }

    public function purgeOperations(mixed $request): mixed
    {
        return $this->instanceRequest('DELETE', '/queue/operations', $request);
    }

    private function removeQueueItem(mixed $request, string $prefix): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $messageId = $body['messageId'] ?? $body['message_id'] ?? null;
        if (!is_scalar($messageId) || trim((string) $messageId) === '') {
            throw new \InvalidArgumentException('messageId is required.');
        }

        return $this->sendRequest('DELETE', '/instances/' . $this->esc($instanceId) . $prefix . $this->esc((string) $messageId), options: new \VZaps\Sdk\VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
