<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();
$instanceId = vzaps_require_config('instanceId');
$instanceToken = vzaps_require_config('instanceToken');

vzaps_print([
    'status' => $client->sessions()->status($instanceId, $instanceToken),
    'qr' => $client->sessions()->qr($instanceId, $instanceToken),
]);

$phone = vzaps_config('pairPhone');
if (is_string($phone) && trim($phone) !== '') {
    vzaps_print(['pairCode' => $client->sessions()->pairCode($instanceId, $phone, $instanceToken)]);
}
