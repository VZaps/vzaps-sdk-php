<?php

declare(strict_types=1);

use VZaps\Sdk\Models\Realtime\EventSubscribeRequest;
use VZaps\Sdk\Models\Realtime\VZapsEventType;

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();

$lastEventId = vzaps_config('lastEventId');
$lastEventId = is_string($lastEventId) && trim($lastEventId) !== '' ? $lastEventId : null;

$subscription = $client->events()->subscribe(new EventSubscribeRequest(
    instanceId: vzaps_require_config('instanceId'),
    instanceToken: vzaps_require_config('instanceToken'),
    events: [VZapsEventType::Message, VZapsEventType::Connected],
    reconnect: true,
    lastEventId: $lastEventId,
));

$subscription->on(VZapsEventType::All, static function ($event): void {
    echo sprintf("[%s] %s\n", $event->type, $event->id);
});

$subscription->awaitClose();
