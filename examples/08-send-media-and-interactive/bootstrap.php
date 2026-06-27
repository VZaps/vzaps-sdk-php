<?php

declare(strict_types=1);

use VZaps\Sdk\VZapsClient;

require __DIR__ . '/vendor/autoload.php';

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    fwrite(STDERR, "Missing config.php. Copy config.example.php to config.php and set your credentials.\n");
    exit(1);
}

/** @var array<string, mixed> $config */
$config = require $configFile;

function vzaps_config(string $key, mixed $default = null): mixed
{
    global $config;

    if (!array_key_exists($key, $config)) {
        return $default;
    }

    $value = $config[$key];
    if ($value === null) {
        return $default;
    }

    if (is_string($value) && trim($value) === '') {
        return $default;
    }

    return $value;
}

function vzaps_require_config(string $key): string
{
    $value = vzaps_config($key);
    if (!is_string($value) || trim($value) === '') {
        throw new RuntimeException(sprintf('Missing or empty config key "%s".', $key));
    }

    return $value;
}

function vzaps_client(): VZapsClient
{
    return new VZapsClient(
        clientToken: vzaps_require_config('clientToken'),
        clientSecret: vzaps_require_config('clientSecret'),
        baseUrl: (string) vzaps_config('baseUrl', 'https://api.vzaps.com'),
        realtimeUrl: (string) vzaps_config('realtimeUrl', 'wss://realtime.vzaps.com'),
    );
}

function vzaps_print(mixed $value): void
{
    echo json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
