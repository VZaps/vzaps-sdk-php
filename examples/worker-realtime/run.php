<?php

declare(strict_types=1);

use VZaps\Sdk\Models\Realtime\EventSubscribeRequest;
use VZaps\Sdk\Models\Realtime\VZapsEventType;

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();
$processed = [];

$subscription = $client->events()->subscribe(new EventSubscribeRequest(
    instanceId: vzaps_require_config('instanceId'),
    instanceToken: vzaps_require_config('instanceToken'),
    events: [VZapsEventType::All],
    reconnect: true,
));

$subscription->on(VZapsEventType::All, static function ($event) use (&$processed): void {
    if (isset($processed[$event->id])) {
        return;
    }

    $processed[$event->id] = true;
    echo 'Processing event ' . $event->id . PHP_EOL;
});

$subscription->awaitClose();
