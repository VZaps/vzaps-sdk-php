<?php

declare(strict_types=1);

use VZaps\Sdk\Models\Messages\SendTextMessageRequest;

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();

vzaps_print($client->instances()->list());

$client->messages()->sendText(new SendTextMessageRequest(
    instanceId: vzaps_require_config('instanceId'),
    instanceToken: vzaps_require_config('instanceToken'),
    phone: (string) vzaps_config('phone', '5511999999999'),
    message: (string) vzaps_config('message', 'Hello from the VZaps PHP SDK'),
));

echo 'Message sent.' . PHP_EOL;
