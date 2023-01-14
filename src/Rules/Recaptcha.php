<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Rules;

use Carbon\CarbonInterval;
use Google\ApiCore\ApiException;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Illuminate\Contracts\Validation\Rule;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException;
use Oneduo\RecaptchaEnterprise\Facades\RecaptchaEnterprise;

class Recaptcha implements Rule
{
    private ?int $reason = null;

    public function __construct(public ?string $action = null, public ?CarbonInterval $interval = null)
    {
    }

    /**
     * @param string $attribute
     * @param string $value
     *
     * @return bool
     *
     * @throws ApiException
     * @throws MissingPropertiesException
     */
    public function passes($attribute, $value): bool
    {
        try {
            $assessment = RecaptchaEnterprise::handle($value);
        } catch (InvalidTokenException $exception) {
            $this->reason = $exception->reason;

            return false;
        }

        $validAction = true;
        $validInterval = true;

        if ($this->action) {
            $validAction = $assessment->hasValidAction($this->action);
        }

        if ($this->interval) {
            $validInterval = $assessment->hasValidTimestamp($this->interval);
        }

        return $assessment->hasValidScore() && $validAction && $validInterval;
    }

    public function action(?string $action = null): static
    {
        $this->action = $action;

        return $this;
    }

    public function validity(?CarbonInterval $interval = null): static
    {
        $this->interval = $interval;

        return $this;
    }

    public function message(): string
    {
        return __('validation.recaptcha', [
            'reason' => InvalidReason::name($this->reason),
        ]);
    }
}
