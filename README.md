# VZaps PHP SDK

[![CI](https://github.com/VZaps/vzaps-sdk-php/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/VZaps/vzaps-sdk-php/actions/workflows/ci.yml) [![SDK Documentation](https://img.shields.io/badge/SDK-Documentation-blue)](https://docs.vzaps.com/en/sdk/php/installation) [![license](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)
[![Packagist](https://img.shields.io/packagist/v/vzaps/sdk.svg)](https://packagist.org/packages/vzaps/sdk)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)

Official PHP client for the [VZaps public API](https://docs.vzaps.com). Send WhatsApp messages, manage instances, configure webhooks, and subscribe to realtime events with a resource-oriented interface.

Works in **PHP 8.1+** with Composer. HTTP uses Guzzle 7 by default. WebSocket realtime is intended for CLI workers and daemons; install `textalk/websocket` or inject a custom WebSocket factory.

---

## Table of contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [Authentication](#authentication)
- [Configuration](#configuration)
- [Resources](#resources)
- [Instance tokens](#instance-tokens)
- [Webhooks](#webhooks)
- [Realtime events](#realtime-events)
- [Error handling](#error-handling)
- [PHP](#php)
- [Documentation](#documentation)

---

## Features

- **Automatic JWT handling** — exchanges `clientToken` + `clientSecret` for a bearer token and refreshes it before expiry.
- **Resource-oriented API** — `instances()`, `messages()`, `webhooks()`, `contacts()`, `groups()`, and `events()` mirror the public HTTP contract.
- **Realtime WebSocket client** — subscribe to instance events with reconnect, resume (`lastEventId`), and server-side ack.
- **Instance token support** — pass `instanceToken` on each instance-scoped request.
- **Request DTOs and arrays** — typed request objects for common calls; arrays for evolving API fields.
- **Extensible transport** — inject custom PSR-18 HTTP clients and WebSocket factories for tests or custom runtimes.

---

## Requirements

| Runtime | Minimum version |
| --- | --- |
| PHP | 8.1+ |
| Composer | 2.x recommended |
| Extension | `ext-json` |

The SDK uses Guzzle 7 for HTTP. Realtime WebSocket support is optional (`textalk/websocket`).

---

## Installation

```bash
composer require vzaps/sdk
```

Optional realtime transport:

```bash
composer require textalk/websocket
```

---

## Quick start

Create credentials in the [VZaps dashboard](https://docs.vzaps.com) (`clientToken` and `clientSecret`), then send a text message:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use VZaps\Sdk\VZapsClient;

$vzaps = new VZapsClient(
    clientToken: getenv('VZAPS_CLIENT_TOKEN'),
    clientSecret: getenv('VZAPS_CLIENT_SECRET'),
);

$vzaps->messages()->sendText([
    'instanceId' => 'VZKB8AU4S4CWY1SLXX4I5WJGRZQMDDFTV6',
    'instanceToken' => getenv('VZAPS_INSTANCE_TOKEN'),
    'phone' => '5511999999999',
    'message' => 'Hello from VZaps',
]);
```

---

## Authentication

VZaps uses a two-step model:

1. **Account credentials** — `clientToken` and `clientSecret` identify your integration. The SDK calls `POST /token` and caches the JWT.
2. **Instance token** — instance-scoped routes also require `X-Instance-Token`. Pass it on each instance-scoped request (see [Instance tokens](#instance-tokens)).

Every authenticated HTTP request sends:

| Header | Value |
| --- | --- |
| `Authorization` | `Bearer <jwt>` |
| `X-Client-Token` | Your client token |
| `X-Instance-Token` | Instance token, on instance-scoped requests |

You rarely need to call `auth()->getAccessToken()` directly — resources attach the token for you. Use it when integrating with custom HTTP logic:

```php
$token = $vzaps->auth()->getAccessToken();
```

---

## Configuration

The SDK connects to the VZaps production platform automatically:

| Service | Endpoint |
| --- | --- |
| REST API | `https://api.vzaps.com` |
| Realtime WebSocket | `wss://realtime.vzaps.com/events/ws` |

Pass options to `new VZapsClient(...)`:

| Option | Type | Default | Description |
| --- | --- | --- | --- |
| `clientToken` | `string` | — | **Required.** Public client token from the dashboard. |
| `clientSecret` | `string` | — | **Required.** Client secret used to obtain JWTs. |
| `baseUrl` | `string` | `https://api.vzaps.com` | REST API base URL. |
| `realtimeUrl` | `string` | `wss://realtime.vzaps.com` | Realtime WebSocket base URL. |
| `timeoutSeconds` | `float` | `30.0` | HTTP request timeout in seconds. |
| `tokenSkewSeconds` | `int` | `60` | Refresh JWT this many seconds before expiry. |
| `httpClient` | `ClientInterface` | Guzzle | Custom PSR-18 HTTP client. |
| `webSocketFactory` | `callable` | `textalk/websocket` when installed | Custom WebSocket constructor. |
| `userAgent` | `string` | `VZaps.SDK.PHP/<version>` | Optional `User-Agent` header on HTTP requests. |

No host configuration is required for production — install the package, pass your credentials, and the client targets the production API and realtime service.

---

## Resources

The client exposes namespaced resources. Responses are decoded JSON arrays so the SDK stays compatible with the evolving [OpenAPI schema](https://docs.vzaps.com/api-reference).

### `$vzaps->instances()`

| Method | HTTP | Description |
| --- | --- | --- |
| `create($request)` | `PUT /instances/create` | Create a WhatsApp instance. |
| `list($request?)` | `POST /instances/list` | List instances (pagination, search, sort). |
| `get($instanceId)` | `POST /instances/get` | Get instance details. |
| `update($instanceId, $data, $instanceToken?)` | `PATCH /instances/:id` | Update instance settings. |
| `restart($instanceId, $instanceToken?)` | `POST /instances/:id/restart` | Restart instance runtime. |

### `$vzaps->messages()`

`$vzaps->messages()` wraps the public WhatsApp send and chat endpoints. The most common calls are shown below; the SDK also exposes the other public message operations documented in the API reference, including media, interactive messages, reactions, polls, downloads, edits, deletes, presence, and read receipts.

```php
$vzaps->messages()->sendText([
    'instanceId' => 'VZ...',
    'instanceToken' => 'instance-token',
    'phone' => '5511999999999',
    'message' => 'Hello',
]);

$vzaps->messages()->sendImage([
    'instanceId' => 'VZ...',
    'instanceToken' => 'instance-token',
    'phone' => '5511999999999',
    'image' => 'https://example.com/photo.jpg',
    'caption' => 'Check this out',
]);
```

Available send helpers include `sendText`, `sendImage`, `sendAudio`, `sendDocument`, `sendVideo`, `sendSticker`, `sendGif`, `sendLocation`, `sendContact`, `sendButtons`, `sendList`, `sendLink`, and `sendPoll`. See the API documentation for complete payload examples.

### `$vzaps->webhooks()`

| Method | HTTP | Description |
| --- | --- | --- |
| `get($instanceId, $instanceToken?)` | `GET /instances/:id/webhook` | Read current webhook configuration. |
| `set($request)` | `POST /instances/:id/webhook` | Configure webhook URL and subscribed events. |

### `$vzaps->contacts()`

| Method | HTTP | Description |
| --- | --- | --- |
| `list($instanceId, $instanceToken?)` | `GET /instances/:id/contact/list` | List contacts for the instance. |
| `add($request)` | `POST /instances/:id/contact/add` | Add a contact. |

### `$vzaps->groups()`

| Method | HTTP | Description |
| --- | --- | --- |
| `list($request)` | `GET /instances/:id/group/list` | List groups (paginated). |
| `get($request)` | `GET /instances/:id/group/info` | Get group metadata by `groupId`. |

### `$vzaps->sessions()`

| Method | HTTP | Description |
| --- | --- | --- |
| `status($instanceId, $instanceToken?)` | `GET /instances/:id/session/status` | Check WhatsApp login state and, when connected, live profile fields. |

`GET /instances/{id}/session/status` returns `SessionStatusResponse`. When `$status->data->connected` is `true`, `$status->data` includes (in order) `phone`, `whatsappJid`, `pushName`, `businessName`, `businessProfile`, `profilePictureId`, `profilePictureUrl`, `profileUrl`, and optional `verifiedName`, `about`, `website`. When disconnected, `$status->data` only has `connected` set to `false`.

Other public namespaces are available as first-class resources too: `sessions()`, `users()`, `queues()`, `typeBots()`, `chatwoot()`, and `chats()`.

### `$vzaps->request($method, $path, $body?, $options?)`

Escape hatch for advanced calls or newly released endpoints:

```php
$instance = $vzaps->request('POST', '/instances/get', ['id' => 'VZ...']);
```

---

## Instance tokens

Instance-scoped routes require the instance token in addition to account credentials. Pass it on each request that targets an instance:

```php
$vzaps->messages()->sendText([
    'instanceId' => 'VZ...',
    'instanceToken' => 'instance-token',
    'phone' => '5511999999999',
    'message' => 'Hello',
]);
```

---

## Webhooks

Configure HTTP callbacks for instance events (same payload shape as realtime `data`, delivered to your URL):

```php
$vzaps->webhooks()->set([
    'instanceId' => 'VZ...',
    'instanceToken' => 'instance-token',
    'webhookURL' => 'https://example.com/webhooks/vzaps',
    'events' => ['Message', 'Connected', 'Disconnected'],
]);
```

Common event types: `Message`, `ReadReceipt`, `Connected`, `Disconnected`, `Presence`, `ChatPresence`, `HistorySync`, `GroupParticipantsAdd`, `GroupParticipantsRemove`, or `All`.

Event payloads (webhook and realtime) use **snake_case**, matching the platform. Incoming media events include `media_url` inside `data` when platform storage is available.

---

## Realtime events

Subscribe to the same events over WebSocket at **`wss://realtime.vzaps.com`**. This is the recommended path for in-app notifications, bots, and dashboards that need low-latency delivery without exposing a public webhook URL.

Realtime is designed for CLI workers, daemons, Laravel commands, Symfony commands, and queue consumers — not for typical web request lifecycles.

### Subscribe

```php
use VZaps\Sdk\Models\Realtime\EventSubscribeRequest;
use VZaps\Sdk\Models\Realtime\VZapsEventType;

$subscription = $vzaps->events()->subscribe(new EventSubscribeRequest(
    instanceId: 'VZ...',
    instanceToken: 'instance-token',
    events: [VZapsEventType::Message, VZapsEventType::Connected, VZapsEventType::Disconnected],
    reconnect: true,
    lastEventId: 'evt_previous_id', // optional resume after disconnect
));

$subscription->on(VZapsEventType::Message, function ($event): void {
    echo $event->data['type'] ?? '', PHP_EOL;
});

$subscription->onError(function (\Throwable $error): void {
    error_log($error->getMessage());
});

// Blocking loop — run in a worker process
$subscription->awaitClose();

// Graceful shutdown from another signal handler
$subscription->close();
```

### Event envelope

Each WebSocket message keeps the platform shape (`snake_case`):

```json
{
  "id": "evt_…",
  "type": "Message",
  "instance_id": "VZ…",
  "created_at": "2026-06-23T22:57:17.000Z",
  "data": {
    "type": "Message",
    "event": { },
    "media_url": "https://…"
  }
}
```

- **`data`** — same payload as webhook delivery (`snake_case`).
- **`media_url`** — present on incoming media messages when platform storage is available.

On the PHP `VZapsEvent` object, use `$event->id`, `$event->type`, `$event->instanceId`, `$event->createdAt`, `$event->data`, and `$event->raw`.

### Delivery and ack

Delivery is **at-least-once**. After your handler runs, the SDK sends an ack automatically on the WebSocket connection. Use `lastEventId` when reconnecting if you need to reduce gaps. Deduplicate on `event.id` in your application if you process events idempotently.

### Subscribe options

| Option | Type | Default | Description |
| --- | --- | --- | --- |
| `instanceId` | `string` | — | **Required.** Instance to watch. |
| `events` | `VZapsEventType[]` or `string[]` | all subscribed | Comma-filtered event types. |
| `instanceToken` | `string` | — | **Required.** Instance token for authorization. |
| `reconnect` | `bool` | `true` | Reconnect after socket close. |
| `maxRetries` | `?int` | unlimited | Max reconnect attempts. |
| `retryDelayMs` | `int` | `1000` | Base delay between reconnects (capped with backoff). |
| `lastEventId` | `?string` | — | Resume cursor after disconnect. |

### Handler registration

| Registration | When it fires |
| --- | --- |
| `onError()` | Handler or transport error. |
| `on(Message)`, `on(Connected)`, … | Matching realtime event type. |
| `on(All)` | Every event type. |

Call `awaitClose()` to run the receive loop. Call `close()` to stop the subscription.

---

## Error handling

The SDK throws typed exceptions you can catch and branch on:

| Class | When |
| --- | --- |
| `VZapsException` | Base class for SDK failures. |
| `VZapsApiException` | HTTP errors include `statusCode`, `errorCode`, and `details`. |
| `VZapsAuthenticationException` | Invalid `clientToken` / `clientSecret` (401/403). |
| `VZapsRateLimitException` | Rate limited (429). |
| `VZapsTimeoutException` | Request exceeded `timeoutSeconds`. |
| `VZapsRealtimeException` | WebSocket or realtime transport failure. |

```php
use VZaps\Sdk\Exceptions\VZapsApiException;
use VZaps\Sdk\Exceptions\VZapsAuthenticationException;
use VZaps\Sdk\Exceptions\VZapsException;
use VZaps\Sdk\Exceptions\VZapsTimeoutException;

try {
    $vzaps->messages()->sendText([
        'instanceId' => $instanceId,
        'instanceToken' => $instanceToken,
        'phone' => $phone,
        'message' => $message,
    ]);
} catch (VZapsAuthenticationException $e) {
    error_log('Check client credentials');
} catch (VZapsTimeoutException $e) {
    error_log('Request timed out');
} catch (VZapsApiException $e) {
    error_log($e->statusCode . ' ' . $e->getMessage());
} catch (VZapsException $e) {
    throw $e;
}
```

Exception messages redact client credentials.

---

## PHP

The SDK uses **camelCase** for PHP method names and request DTO properties. Outgoing JSON is converted to **snake_case** on the wire when the public API expects it. **Realtime and webhook event payloads stay in snake_case** so both delivery channels match.

Exported request types include options, events, and common payloads:

```php
use VZaps\Sdk\Models\Common\InstanceCreateRequest;
use VZaps\Sdk\Models\Messages\SendTextMessageRequest;
use VZaps\Sdk\Models\Realtime\EventSubscribeRequest;
use VZaps\Sdk\Models\Realtime\VZapsEvent;
use VZaps\Sdk\Models\Realtime\VZapsEventType;
use VZaps\Sdk\VZapsClient;
```

Resources accept arrays or DTOs. Responses are `array<string, mixed>` by default:

```php
/** @var array<string, mixed> $page */
$page = $vzaps->instances()->list([
    'page' => 1,
    'pageSize' => 20,
]);

$vzaps->messages()->sendText(new SendTextMessageRequest(
    instanceId: 'VZ...',
    instanceToken: getenv('VZAPS_INSTANCE_TOKEN'),
    phone: '5511999999999',
    message: 'Hello from VZaps',
));
```

For new or advanced public contract fields, use arrays or `GenericInstanceRequest`.

---

## Documentation

- [VZaps docs](https://docs.vzaps.com)
- [API reference (OpenAPI)](https://docs.vzaps.com/api-reference)
- [Postman collections](https://docs.vzaps.com/postman/)
- [Report an issue](https://github.com/VZaps/vzaps-sdk-php/issues)

---

## License

MIT © VZaps
