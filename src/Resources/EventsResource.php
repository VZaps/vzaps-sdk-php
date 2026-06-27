<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\Http\VZapsHttpClient;
use VZaps\Sdk\Models\Realtime\EventSubscribeRequest;
use VZaps\Sdk\Realtime\EventSubscription;

final class EventsResource extends BaseResource
{
    /**
     * @param null|\Closure(string, array<string, string>): \VZaps\Sdk\Realtime\WebSocketConnection $webSocketFactory
     */
    public function __construct(VZapsHttpClient $http, private readonly ?\Closure $webSocketFactory = null)
    {
        parent::__construct($http);
    }

    public function subscribe(EventSubscribeRequest $request): EventSubscription
    {
        return (new EventSubscription($this->http, $request, $this->webSocketFactory))->open();
    }
}
