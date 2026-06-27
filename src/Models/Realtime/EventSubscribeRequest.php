<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Realtime;

final class EventSubscribeRequest
{
    /**
     * @param list<VZapsEventType|string> $events
     */
    public function __construct(
        public readonly string $instanceId,
        public readonly string $instanceToken,
        public readonly array $events = [],
        public readonly bool $reconnect = true,
        public readonly ?int $maxRetries = null,
        public readonly int $retryDelayMs = 1000,
        public readonly ?string $lastEventId = null,
    ) {
    }

    /**
     * @return list<string>
     */
    public function eventNames(): array
    {
        return array_map(
            static fn (VZapsEventType|string $event): string => $event instanceof VZapsEventType ? $event->value : $event,
            $this->events,
        );
    }
}
