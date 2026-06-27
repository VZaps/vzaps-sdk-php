<?php

declare(strict_types=1);

namespace VZaps\Sdk\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use VZaps\Sdk\Exceptions\VZapsRateLimitException;
use VZaps\Sdk\Models\Common\InstanceCreateRequest;
use VZaps\Sdk\Models\Messages\SendTextMessageRequest;
use VZaps\Sdk\Models\Realtime\EventSubscribeRequest;
use VZaps\Sdk\Models\Realtime\VZapsEventType;
use VZaps\Sdk\Realtime\WebSocketConnection;
use VZaps\Sdk\VZapsClient;
use VZaps\Sdk\VZapsClientOptions;

final class VZapsClientTest extends TestCase
{
    public function testCachesTokenAndSendsAuthHeaders(): void
    {
        $history = [];
        $client = $this->client([
            new Response(200, [], '{"access_token":"jwt-token","expires_in":3600}'),
            new Response(200, [], '{"items":[]}'),
            new Response(200, [], '{"items":[]}'),
        ], $history);

        $client->instances()->list();
        $client->instances()->list();

        self::assertCount(3, $history);
        self::assertSame('/token', $history[0]['request']->getUri()->getPath());
        self::assertSame('Bearer jwt-token', $history[1]['request']->getHeaderLine('Authorization'));
        self::assertSame('client-token', $history[1]['request']->getHeaderLine('X-Client-Token'));
        self::assertSame('/instances/list', $history[1]['request']->getUri()->getPath());
    }

    public function testSerializesCreateInstanceRequestToSnakeCase(): void
    {
        $history = [];
        $client = $this->client([
            new Response(200, [], '{"access_token":"jwt-token","expires_in":3600}'),
            new Response(200, [], '{"id":"instance-1"}'),
        ], $history);

        $client->instances()->create(new InstanceCreateRequest(
            name: 'Support',
            eventsSubscribe: ['Message', 'Connected'],
        ));

        self::assertSame('/instances/create', $history[1]['request']->getUri()->getPath());
        self::assertJsonStringEqualsJsonString(
            '{"name":"Support","events_subscribe":["Message","Connected"]}',
            (string) $history[1]['request']->getBody(),
        );
    }

    public function testMessageRequestUsesInstanceHeaderAndRemovesInstanceFieldsFromBody(): void
    {
        $history = [];
        $client = $this->client([
            new Response(200, [], '{"access_token":"jwt-token","expires_in":3600}'),
            new Response(200, [], '{"ok":true}'),
        ], $history);

        $client->messages()->sendText(new SendTextMessageRequest(
            instanceId: 'instance-1',
            instanceToken: 'instance-token',
            phone: '5511999999999',
            message: 'Hello from PHP',
        ));

        self::assertSame('/instances/instance-1/chat/send/text', $history[1]['request']->getUri()->getPath());
        self::assertSame('instance-token', $history[1]['request']->getHeaderLine('X-Instance-Token'));
        self::assertJsonStringEqualsJsonString(
            '{"phone":"5511999999999","message":"Hello from PHP"}',
            (string) $history[1]['request']->getBody(),
        );
    }

    public function testMapsRateLimitErrors(): void
    {
        $history = [];
        $client = $this->client([
            new Response(200, [], '{"access_token":"jwt-token","expires_in":3600}'),
            new Response(429, ['X-Request-Id' => 'req_123'], '{"message":"Slow down","code":"rate_limited"}'),
        ], $history);

        $this->expectException(VZapsRateLimitException::class);
        $this->expectExceptionMessage('Slow down');

        $client->instances()->list();
    }

    public function testRealtimeDispatchesHandlersAndAcknowledgesEvents(): void
    {
        $history = [];
        $fakeSocket = new FakeWebSocketConnection([
            '{"id":"evt_1","type":"Message","instance_id":"instance-1","created_at":"2026-01-01T00:00:00Z","data":{"text":"hi"}}',
        ]);

        $client = $this->client([
            new Response(200, [], '{"access_token":"jwt-token","expires_in":3600}'),
        ], $history, static fn (string $url, array $headers): WebSocketConnection => $fakeSocket);

        $subscription = $client->events()->subscribe(new EventSubscribeRequest(
            instanceId: 'instance-1',
            instanceToken: 'instance-token',
            events: [VZapsEventType::Message],
            reconnect: false,
        ));

        $seen = [];
        $subscription->on(VZapsEventType::Message, function ($event) use (&$seen, $subscription): void {
            $seen[] = $event->id;
            $subscription->close();
        });

        $subscription->awaitClose();

        self::assertSame(['evt_1'], $seen);
        self::assertSame(['{"type":"ack","event_id":"evt_1"}'], $fakeSocket->sent);
    }

    /**
     * @param list<Response> $responses
     * @param array<int, array<string, mixed>>|\ArrayAccess<int, array<string, mixed>> $history
     */
    private function client(array $responses, array|\ArrayAccess &$history, ?callable $webSocketFactory = null): VZapsClient
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        return new VZapsClient(new VZapsClientOptions(
            clientToken: 'client-token',
            clientSecret: 'client-secret',
            baseUrl: 'https://api.test',
            realtimeUrl: 'wss://realtime.test',
            httpClient: new Client(['handler' => $stack, 'http_errors' => false]),
            webSocketFactory: $webSocketFactory,
        ));
    }
}

final class FakeWebSocketConnection implements WebSocketConnection
{
    /**
     * @param list<string> $messages
     */
    public function __construct(private array $messages)
    {
    }

    /**
     * @var list<string>
     */
    public array $sent = [];

    public function receive(): ?string
    {
        return array_shift($this->messages);
    }

    public function send(string $message): void
    {
        $this->sent[] = $message;
    }

    public function close(int $code = 1000, string $reason = 'Client closed subscription'): void
    {
    }
}
