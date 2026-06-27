<?php

declare(strict_types=1);

namespace VZaps\Sdk\Realtime;

interface WebSocketConnection
{
    public function receive(): ?string;

    public function send(string $message): void;

    public function close(int $code = 1000, string $reason = 'Client closed subscription'): void;
}
