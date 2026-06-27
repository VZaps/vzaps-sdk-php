<?php

declare(strict_types=1);

use VZaps\Sdk\Models\Common\InstanceCreateRequest;

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();

vzaps_print($client->instances()->create(new InstanceCreateRequest(
    name: (string) vzaps_config('newInstanceName', 'PHP SDK Example'),
    eventsSubscribe: ['Message', 'Connected', 'Disconnected'],
)));
