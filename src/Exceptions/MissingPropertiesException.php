<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Exceptions;

use Exception;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;

class MissingPropertiesException extends Exception
{
    public function __construct(public ?Assessment $assessment = null)
    {
        parent::__construct('No properties provided in the assessment');
    }

    public static function forAssessment(?Assessment $assessment = null): self
    {
        return new self(assessment: $assessment);
    }

    /**
     * @codeCoverageIgnore
     */
    public function context(): array
    {
        return [
            'assessment' => $this->assessment?->serializeToJsonString(),
        ];
    }
}
