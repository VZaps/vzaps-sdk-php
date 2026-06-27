<?php

declare(strict_types=1);

namespace VZaps\Sdk;

use Psr\Http\Client\ClientInterface;
use VZaps\Sdk\Http\VZapsHttpClient;
use VZaps\Sdk\Resources\AuthResource;
use VZaps\Sdk\Resources\ChatsResource;
use VZaps\Sdk\Resources\ChatwootResource;
use VZaps\Sdk\Resources\ContactsResource;
use VZaps\Sdk\Resources\EventsResource;
use VZaps\Sdk\Resources\GroupsResource;
use VZaps\Sdk\Resources\InstancesResource;
use VZaps\Sdk\Resources\MessagesResource;
use VZaps\Sdk\Resources\QueuesResource;
use VZaps\Sdk\Resources\SessionsResource;
use VZaps\Sdk\Resources\TypeBotsResource;
use VZaps\Sdk\Resources\UsersResource;
use VZaps\Sdk\Resources\WebhooksResource;

final class VZapsClient
{
    private readonly VZapsHttpClient $http;
    private readonly AuthResource $auth;
    private readonly InstancesResource $instances;
    private readonly SessionsResource $sessions;
    private readonly MessagesResource $messages;
    private readonly WebhooksResource $webhooks;
    private readonly ContactsResource $contacts;
    private readonly GroupsResource $groups;
    private readonly UsersResource $users;
    private readonly QueuesResource $queues;
    private readonly TypeBotsResource $typeBots;
    private readonly ChatwootResource $chatwoot;
    private readonly ChatsResource $chats;
    private readonly EventsResource $events;

    public function __construct(
        string|VZapsClientOptions $clientToken,
        ?string $clientSecret = null,
        ?string $baseUrl = null,
        ?string $realtimeUrl = null,
        float $timeoutSeconds = 30.0,
        int $tokenSkewSeconds = 60,
        ?string $userAgent = null,
        ?ClientInterface $httpClient = null,
        ?callable $webSocketFactory = null,
    ) {
        $options = $clientToken instanceof VZapsClientOptions
            ? $clientToken
            : new VZapsClientOptions(
                clientToken: $clientToken,
                clientSecret: $clientSecret ?? '',
                baseUrl: $baseUrl,
                realtimeUrl: $realtimeUrl,
                timeoutSeconds: $timeoutSeconds,
                tokenSkewSeconds: $tokenSkewSeconds,
                userAgent: $userAgent,
                httpClient: $httpClient,
                webSocketFactory: $webSocketFactory,
            );

        $this->http = new VZapsHttpClient($options);
        $this->auth = new AuthResource($this->http);
        $this->instances = new InstancesResource($this->http);
        $this->sessions = new SessionsResource($this->http);
        $this->messages = new MessagesResource($this->http);
        $this->webhooks = new WebhooksResource($this->http);
        $this->contacts = new ContactsResource($this->http);
        $this->groups = new GroupsResource($this->http);
        $this->users = new UsersResource($this->http);
        $this->queues = new QueuesResource($this->http);
        $this->typeBots = new TypeBotsResource($this->http);
        $this->chatwoot = new ChatwootResource($this->http);
        $this->chats = new ChatsResource($this->http);
        $this->events = new EventsResource($this->http, $options->webSocketFactory);
    }

    public function auth(): AuthResource
    {
        return $this->auth;
    }

    public function instances(): InstancesResource
    {
        return $this->instances;
    }

    public function sessions(): SessionsResource
    {
        return $this->sessions;
    }

    public function messages(): MessagesResource
    {
        return $this->messages;
    }

    public function webhooks(): WebhooksResource
    {
        return $this->webhooks;
    }

    public function contacts(): ContactsResource
    {
        return $this->contacts;
    }

    public function groups(): GroupsResource
    {
        return $this->groups;
    }

    public function users(): UsersResource
    {
        return $this->users;
    }

    public function queues(): QueuesResource
    {
        return $this->queues;
    }

    public function typeBots(): TypeBotsResource
    {
        return $this->typeBots;
    }

    public function chatwoot(): ChatwootResource
    {
        return $this->chatwoot;
    }

    public function chats(): ChatsResource
    {
        return $this->chats;
    }

    public function events(): EventsResource
    {
        return $this->events;
    }

    public function request(string $method, string $path, mixed $body = null, ?VZapsRequestOptions $options = null): mixed
    {
        return $this->http->request($method, $path, $body, $options);
    }
}
