<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();
$request = [
    'instanceId' => vzaps_require_config('instanceId'),
    'instanceToken' => vzaps_require_config('instanceToken'),
];

vzaps_print([
    'messages' => $client->queues()->listMessages($request),
    'operations' => $client->queues()->listOperations($request),
]);
