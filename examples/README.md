# VZaps PHP SDK Examples

Runnable Composer projects that consume the published SDK from Packagist (`vzaps/sdk`).

Each numbered folder is a standalone project with its own `composer.json`, `config.example.php`, and `run.php`. You do **not** need to clone the full SDK repository to run one example.

## Prerequisites

- PHP 8.1 or later
- Composer 2.x
- `ext-json`

Realtime examples (`06-realtime-subscribe`, `worker-realtime`) also require the WebSocket transport declared in their `composer.json` (`textalk/websocket`).

## Option A — one example folder (recommended)

Download only one example folder, for example [`07-send-text-message`](https://github.com/VZaps/vzaps-sdk-php/tree/main/examples/07-send-text-message):

1. Open the folder on GitHub and choose **Download ZIP**, or run:

```bash
npx --yes degit VZaps/vzaps-sdk-php/examples/07-send-text-message vzaps-php-send-text
cd vzaps-php-send-text
```

2. Configure credentials:

```bash
cp config.example.php config.php
```

Edit `config.php`:

```php
return [
    'clientToken' => 'your-client-token',
    'clientSecret' => 'your-client-secret',
    'instanceId' => 'VZ...',
    'instanceToken' => 'your-instance-token',
    'phone' => '5511999999999',
    'message' => 'Hello from VZaps PHP SDK',
];
```

Each example only documents the keys it needs in `config.example.php`.

3. Install dependencies and run:

```bash
composer install
php run.php
```

Or:

```bash
composer start
```

## Option B — sparse checkout

```bash
git clone --depth 1 --filter=blob:none --sparse https://github.com/VZaps/vzaps-sdk-php.git
cd vzaps-sdk-php
git sparse-checkout set examples/07-send-text-message
cd examples/07-send-text-message
cp config.example.php config.php
composer install
php run.php
```

## Option C — full repository clone

Use this when you want every example locally or you are developing the SDK:

```bash
git clone https://github.com/VZaps/vzaps-sdk-php.git
cd vzaps-sdk-php/examples/07-send-text-message
cp config.example.php config.php
composer install
php run.php
```

When testing local SDK changes before Packagist publish:

```bash
cd examples/07-send-text-message
composer config repositories.vzaps path ../..
composer require vzaps/sdk:dev-main
composer install
php run.php
```

## Modules

1. `01-auth-and-list-instances`
2. `02-create-instance`
3. `03-instance-subscription`
4. `04-session-and-pairing`
5. `05-configure-webhook`
6. `06-realtime-subscribe`
7. `07-send-text-message`
8. `08-send-media-and-interactive`
9. `09-send-poll-reaction-and-chat-actions`
10. `10-queues`
11. `11-typebot-and-chatwoot`
12. `quickstart`
13. `worker-realtime`

## Framework snippets

- `laravel/` — configuration snippet only (not a runnable Composer project yet)
- `symfony/` — configuration snippet only (not a runnable Composer project yet)

## Coverage

- Auth and instance listing
- Instance creation and billing subscription checkout
- Session status, QR, and phone pairing code
- Webhook and realtime subscription
- Text, media, buttons, list, poll, reactions, presence
- Queue list/remove/purge examples
- TypeBot and Chatwoot integration examples
