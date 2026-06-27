<?php

declare(strict_types=1);

namespace VZaps\Sdk\Realtime;

use Throwable;
use VZaps\Sdk\Exceptions\VZapsRealtimeException;
use VZaps\Sdk\Http\VZapsHttpClient;
use VZaps\Sdk\Json\VZapsJson;
use VZaps\Sdk\Models\Realtime\EventSubscribeRequest;
use VZaps\Sdk\Models\Realtime\VZapsEvent;
use VZaps\Sdk\Models\Realtime\VZapsEventType;

final class EventSubscription
{
    /**
     * @var array<string, list<callable(VZapsEvent): void>>
     */
    private array $handlers = [];

    /**
     * @var list<callable(Throwable): void>
     */
    private array $errorHandlers = [];

    private ?WebSocketConnection $connection = null;
    private bool $closed = false;
    private ?string $lastEventId;

    /**
     * @param null|\Closure(string, array<string, string>): WebSocketConnection $webSocketFactory
     */
    public function __construct(
        private readonly VZapsHttpClient $http,
        private readonly EventSubscribeRequest $request,
        private readonly ?\Closure $webSocketFactory = null,
    ) {
        $this->lastEventId = $request->lastEventId;
    }

    public function open(): self
    {
        $token = $this->http->getAccessToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Client-Token' => $this->http->authHeaders()['X-Client-Token'],
            'X-Instance-Token' => $this->request->instanceToken,
        ];

        $url = $this->http->buildRealtimeUrl('/events/ws', [
            'instanceId' => $this->request->instanceId,
            'events' => $this->request->eventNames() === [] ? null : implode(',', $this->request->eventNames()),
            'accessToken' => $token,
            'clientToken' => $headers['X-Client-Token'],
            'instanceToken' => $this->request->instanceToken,
            'lastEventId' => $this->lastEventId,
        ]);

        $this->connection = $this->webSocketFactory === null
            ? new DefaultWebSocketConnection($url, $headers)
            : ($this->webSocketFactory)($url, $headers);

        return $this;
    }

    /**
     * @param VZapsEventType|string $event
     * @param callable(VZapsEvent): void $handler
     */
    public function on(VZapsEventType|string $event, callable $handler): self
    {
        $key = $event instanceof VZapsEventType ? $event->value : $event;
        $this->handlers[$key] ??= [];
        $this->handlers[$key][] = $handler;

        return $this;
    }

    /**
     * @param callable(Throwable): void $handler
     */
    public function onError(callable $handler): self
    {
        $this->errorHandlers[] = $handler;

        return $this;
    }

    public function awaitClose(): void
    {
        $attempt = 0;

        while (!$this->closed) {
            try {
                $this->ensureOpen();
                $raw = $this->connection?->receive();

                if ($raw === null) {
                    throw new VZapsRealtimeException('Realtime connection closed.');
                }

                $this->dispatch($raw);
                $attempt = 0;
            } catch (Throwable $exception) {
                $this->emitError($exception);

                if ($this->closed || !$this->request->reconnect) {
                    throw $exception;
                }

                $attempt++;
                if ($this->request->maxRetries !== null && $attempt > $this->request->maxRetries) {
                    throw new VZapsRealtimeException('Realtime reconnect limit reached.', previous: $exception);
                }

                usleep($this->reconnectDelayMs($attempt) * 1000);
                $this->open();
            }
        }
    }

    public function close(int $code = 1000, string $reason = 'Client closed subscription'): void
    {
        $this->closed = true;
        $this->connection?->close($code, $reason);
    }

    private function ensureOpen(): void
    {
        if ($this->connection === null) {
            $this->open();
        }
    }

    private function dispatch(string $raw): void
    {
        $payload = VZapsJson::decode($raw);
        if (!is_array($payload)) {
            throw new VZapsRealtimeException('Realtime event payload is not a JSON object.');
        }

        $event = VZapsEvent::fromArray($payload);
        $this->lastEventId = $event->id !== '' ? $event->id : $this->lastEventId;

        $handlers = array_merge(
            $this->handlers[$event->type] ?? [],
            $this->handlers[VZapsEventType::All->value] ?? [],
        );

        foreach ($handlers as $handler) {
            $handler($event);
        }

        if ($event->id !== '') {
            $this->connection?->send(VZapsJson::encode(['type' => 'ack', 'eventId' => $event->id]));
        }
    }

    private function reconnectDelayMs(int $attempt): int
    {
        return min(30000, max($this->request->retryDelayMs, $this->request->retryDelayMs * $attempt));
    }

    private function emitError(Throwable $exception): void
    {
        foreach ($this->errorHandlers as $handler) {
            $handler($exception);
        }
    }
}
