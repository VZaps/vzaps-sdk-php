<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();
$instanceId = vzaps_require_config('instanceId');
$instanceToken = vzaps_require_config('instanceToken');

vzaps_print([
    'typebots' => $client->typeBots()->list($instanceId, $instanceToken),
    'chatwoot' => $client->chatwoot()->get($instanceId, $instanceToken),
]);
