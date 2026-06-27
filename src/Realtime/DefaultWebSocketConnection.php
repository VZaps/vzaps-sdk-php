<?php

declare(strict_types=1);

namespace VZaps\Sdk\Realtime;

use VZaps\Sdk\Exceptions\VZapsRealtimeException;

final class DefaultWebSocketConnection implements WebSocketConnection
{
    private object $client;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $url, array $headers)
    {
        if (!class_exists(\WebSocket\Client::class)) {
            throw new VZapsRealtimeException('Install textalk/websocket to use realtime subscriptions, or pass a custom webSocketFactory.');
        }

        $this->client = new \WebSocket\Client($url, ['headers' => $headers]);
    }

    public function receive(): ?string
    {
        $message = $this->client->receive();

        return is_string($message) ? $message : null;
    }

    public function send(string $message): void
    {
        $this->client->send($message);
    }

    public function close(int $code = 1000, string $reason = 'Client closed subscription'): void
    {
        if (method_exists($this->client, 'close')) {
            $this->client->close($code, $reason);
        }
    }
}
