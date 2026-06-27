<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();

vzaps_print($client->webhooks()->set([
    'instanceId' => vzaps_require_config('instanceId'),
    'instanceToken' => vzaps_require_config('instanceToken'),
    'webhookURL' => (string) vzaps_config('webhookUrl', 'https://example.com/webhooks/vzaps'),
    'events' => ['Message', 'Connected', 'Disconnected'],
]));
