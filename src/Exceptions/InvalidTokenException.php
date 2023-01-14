<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Exceptions;

use Exception;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Throwable;

class InvalidTokenException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public readonly ?int $reason = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function make(?int $reason = null): static
    {
        return new static(
            message: sprintf(
                'Invalid token, reason: %s',
                InvalidReason::name($reason) ?? 'Unknown reason'
            ),
            reason: $reason
        );
    }

    public function context(): array
    {
        return [
            'reason' => InvalidReason::name($this->reason),
            'code' => $this->reason,
        ];
    }
}
