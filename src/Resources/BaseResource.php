<?php

declare(strict_types=1);

namespace VZaps\Sdk\Resources;

use VZaps\Sdk\Http\VZapsHttpClient;
use VZaps\Sdk\Models\Common\InstanceScopedRequest;
use VZaps\Sdk\VZapsRequestOptions;

abstract class BaseResource
{
    public function __construct(protected readonly VZapsHttpClient $http)
    {
    }

    /**
     * @param mixed $body
     * @return mixed
     */
    protected function sendRequest(string $method, string $path, mixed $body = null, ?VZapsRequestOptions $options = null): mixed
    {
        return $this->http->request($method, $path, $body, $options);
    }

    /**
     * @param mixed $request
     * @return array{0: string, 1: string, 2: array<string, mixed>}
     */
    protected function splitInstanceRequest(mixed $request): array
    {
        $data = $this->toArray($request);
        $instanceId = $data['instanceId'] ?? $data['instance_id'] ?? null;
        $instanceToken = $data['instanceToken'] ?? $data['instance_token'] ?? null;

        if (!is_scalar($instanceId) || trim((string) $instanceId) === '') {
            throw new \InvalidArgumentException('instanceId is required.');
        }

        if (!is_scalar($instanceToken) || trim((string) $instanceToken) === '') {
            throw new \InvalidArgumentException('instanceToken is required.');
        }

        unset($data['instanceId'], $data['instance_id'], $data['instanceToken'], $data['instance_token']);

        return [(string) $instanceId, (string) $instanceToken, $data];
    }

    /**
     * @param mixed $request
     * @return array<string, mixed>
     */
    protected function toArray(mixed $request): array
    {
        if ($request instanceof InstanceScopedRequest) {
            return $request->toArray();
        }

        if ($request instanceof \JsonSerializable) {
            $value = $request->jsonSerialize();
            if (is_array($value)) {
                return $value;
            }
        }

        if (is_object($request) && method_exists($request, 'toArray')) {
            $value = $request->toArray();
            if (is_array($value)) {
                return $value;
            }
        }

        if (is_array($request)) {
            return $request;
        }

        if ($request === null) {
            return [];
        }

        throw new \InvalidArgumentException('Request must be an array or a VZaps model.');
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>|null
     */
    protected function bodyOrNull(array $body): ?array
    {
        return $body === [] ? null : $body;
    }

    protected function esc(string $value): string
    {
        return rawurlencode($value);
    }

    /**
     * @param mixed $request
     * @return mixed
     */
    protected function instanceRequest(string $method, string $suffix, mixed $request): mixed
    {
        [$instanceId, $instanceToken, $body] = $this->splitInstanceRequest($request);

        return $this->sendRequest(
            $method,
            '/instances/' . $this->esc($instanceId) . $suffix,
            $this->bodyOrNull($body),
            new VZapsRequestOptions(instanceToken: $instanceToken),
        );
    }
}
