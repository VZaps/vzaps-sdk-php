<?php

declare(strict_types=1);

use VZaps\Sdk\Models\Common\GenericInstanceRequest;

require __DIR__ . '/bootstrap.php';

$client = vzaps_client();
$scope = [
    'instanceId' => vzaps_require_config('instanceId'),
    'instanceToken' => vzaps_require_config('instanceToken'),
    'phone' => (string) vzaps_config('phone', '5511999999999'),
];

vzaps_print($client->messages()->sendImage(new GenericInstanceRequest(
    instanceId: $scope['instanceId'],
    instanceToken: $scope['instanceToken'],
    data: [
        'phone' => $scope['phone'],
        'image' => (string) vzaps_config('imageUrl', 'https://example.com/image.jpg'),
        'caption' => 'Image sent from PHP',
    ],
)));

vzaps_print($client->messages()->sendButtons(array_replace($scope, [
    'message' => 'Choose an option',
    'buttons' => [
        ['id' => 'yes', 'text' => 'Yes'],
        ['id' => 'no', 'text' => 'No'],
    ],
])));
