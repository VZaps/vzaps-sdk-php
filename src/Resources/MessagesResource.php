<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\Models\Messages\SendTextMessageRequest;
use VZaps\Sdk\VZapsRequestOptions;

final class MessagesResource extends BaseResource
{
    public function sendText(SendTextMessageRequest|array $request): mixed
    {
        return $this->message('POST', '/chat/send/text', $request);
    }

    public function sendImage(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/image', $request);
    }

    public function sendAudio(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/audio', $request);
    }

    public function sendDocument(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/document', $request);
    }

    public function sendVideo(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/video', $request);
    }

    public function sendSticker(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/sticker', $request);
    }

    public function sendGif(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/gif', $request);
    }

    public function sendLocation(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/location', $request);
    }

    public function sendContact(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/contact', $request);
    }

    public function sendButtons(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/buttons', $request);
    }

    public function sendList(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/list', $request);
    }

    public function sendLink(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/link', $request);
    }

    public function sendPoll(mixed $request): mixed
    {
        return $this->message('POST', '/chat/send/poll', $request);
    }

    public function pollVote(mixed $request): mixed
    {
        return $this->message('POST', '/chat/poll/vote', $request);
    }

    public function react(mixed $request): mixed
    {
        return $this->message('POST', '/chat/react', $request);
    }

    public function removeReaction(mixed $request): mixed
    {
        return $this->message('DELETE', '/chat/react', $request);
    }

    public function presence(mixed $request): mixed
    {
        return $this->message('POST', '/chat/presence', $request);
    }

    public function markRead(mixed $request): mixed
    {
        return $this->message('POST', '/chat/markread', $request);
    }

    public function edit(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $messageId = $body['messageId'] ?? $body['message_id'] ?? null;
        if (!is_scalar($messageId) || trim((string) $messageId) === '') {
            throw new \InvalidArgumentException('messageId is required.');
        }

        unset($body['messageId'], $body['message_id']);

        return $this->sendRequest('PATCH', '/instances/' . $this->esc($instanceId) . '/chat/messages/' . $this->esc((string) $messageId), $body, new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function delete(mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);
        $messageId = $body['messageId'] ?? $body['message_id'] ?? null;
        if (!is_scalar($messageId) || trim((string) $messageId) === '') {
            throw new \InvalidArgumentException('messageId is required.');
        }

        unset($body['messageId'], $body['message_id']);

        return $this->sendRequest('DELETE', '/instances/' . $this->esc($instanceId) . '/chat/messages/' . $this->esc((string) $messageId), $this->bodyOrNull($body), new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    public function downloadImage(mixed $request): mixed
    {
        return $this->message('POST', '/chat/downloadimage', $request);
    }

    public function downloadVideo(mixed $request): mixed
    {
        return $this->message('POST', '/chat/downloadvideo', $request);
    }

    public function downloadAudio(mixed $request): mixed
    {
        return $this->message('POST', '/chat/downloadaudio', $request);
    }

    public function downloadDocument(mixed $request): mixed
    {
        return $this->message('POST', '/chat/downloaddocument', $request);
    }

    public function send(string $instanceId, string $path, array $body, ?string $instanceToken = null): mixed
    {
        return $this->sendRequest('POST', '/instances/' . $this->esc($instanceId) . '/chat/' . ltrim($path, '/'), $body, new VZapsRequestOptions(instanceToken: $instanceToken));
    }

    private function message(string $method, string $suffix, mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);

        return $this->sendRequest($method, '/instances/' . $this->esc($instanceId) . $suffix, $this->bodyOrNull($body), new VZapsRequestOptions(instanceToken: $instanceToken));
    }
}
