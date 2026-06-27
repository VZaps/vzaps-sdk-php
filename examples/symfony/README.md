# Symfony Example

Configure the client as a service:

```yaml
services:
  VZaps\Sdk\VZapsClient:
    arguments:
      $clientToken: '%env(VZAPS_CLIENT_TOKEN)%'
      $clientSecret: '%env(VZAPS_CLIENT_SECRET)%'
```

Use realtime from a Symfony Console command or Messenger worker.
