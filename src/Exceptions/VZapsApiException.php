<?php

declare(strict_types=1);

namespace VZaps\Sdk\Exceptions;

class VZapsApiException extends VZapsException
{
    /**
     * @param mixed $details
     */
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?string $errorCode = null,
        public readonly mixed $details = null,
        public readonly ?string $requestId = null,
        public readonly ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
