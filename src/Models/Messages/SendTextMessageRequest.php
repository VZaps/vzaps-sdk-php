<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Messages;

use VZaps\Sdk\Models\Common\InstanceScopedRequest;

final class SendTextMessageRequest extends InstanceScopedRequest
{
    /**
     * @param array<string, mixed> $extra
     */
    public function __construct(
        string $instanceId,
        string $instanceToken,
        public readonly string $phone,
        public readonly string $message,
        public readonly array $extra = [],
    ) {
        parent::__construct($instanceId, $instanceToken);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_replace($this->scope(), [
            'phone' => $this->phone,
            'message' => $this->message,
        ], $this->extra);
    }
}
