# Laravel Example

Register the SDK as a singleton in a service provider:

```php
use VZaps\Sdk\VZapsClient;

$this->app->singleton(VZapsClient::class, fn () => new VZapsClient(
    clientToken: config('services.vzaps.client_token'),
    clientSecret: config('services.vzaps.client_secret'),
));
```

Use realtime from an Artisan command or queue worker, not from a normal HTTP request lifecycle.
