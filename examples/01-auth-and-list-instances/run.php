<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();

echo 'Access token:' . PHP_EOL;
echo $client->auth()->getAccessToken() . PHP_EOL . PHP_EOL;

echo 'Instances:' . PHP_EOL;
vzaps_print($client->instances()->list(['page' => 1, 'size' => 20]));
