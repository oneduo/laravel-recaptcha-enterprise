<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Exceptions;

use Exception;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Throwable;

class MissingPropertiesException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        private readonly ?Assessment $assessment = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function make(?Assessment $assessment = null): static
    {
        return new static(
            message: 'Missing properties in the assessment',
            assessment: $assessment,
        );
    }

    public function context(): array
    {
        return [
            'assessment' => $this->assessment,
        ];
    }
}
