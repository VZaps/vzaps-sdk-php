<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();
$request = [
    'instanceId' => vzaps_require_config('instanceId'),
    'instanceToken' => vzaps_require_config('instanceToken'),
    'phone' => (string) vzaps_config('phone', '5511999999999'),
];

vzaps_print($client->messages()->sendPoll(array_replace($request, [
    'name' => 'Pick one',
    'options' => ['A', 'B'],
    'selectableOptionsCount' => 1,
])));

$messageId = vzaps_config('messageId');
if (is_string($messageId) && trim($messageId) !== '') {
    vzaps_print($client->messages()->react(array_replace($request, [
        'messageId' => $messageId,
        'reaction' => '+1',
    ])));

    vzaps_print($client->chats()->read($request));
}
