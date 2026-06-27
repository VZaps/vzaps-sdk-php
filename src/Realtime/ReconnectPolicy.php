<?php

declare(strict_types=1);

namespace VZaps\Sdk\Realtime;

final class ReconnectPolicy
{
    public function __construct(
        public readonly bool $enabled = true,
        public readonly ?int $maxRetries = null,
        public readonly int $initialDelayMs = 1000,
        public readonly int $maxDelayMs = 30000,
    ) {
    }

    public function delayForAttempt(int $attempt): int
    {
        return min($this->maxDelayMs, max($this->initialDelayMs, $this->initialDelayMs * max(1, $attempt)));
    }
}
