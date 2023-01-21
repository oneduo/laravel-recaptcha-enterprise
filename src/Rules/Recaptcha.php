<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Rules;

use Carbon\CarbonInterval;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Illuminate\Contracts\Validation\Rule;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Facades\RecaptchaEnterprise;

class Recaptcha implements Rule
{
    protected ?int $reason = null;

    public function __construct(public ?string $action = null, public ?CarbonInterval $interval = null)
    {
    }

    /**
     * @param  string  $attribute
     * @param  string  $value
     * @return bool
     *
     * @throws \Google\ApiCore\ApiException
     * @throws \Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException
     */
    public function passes($attribute, $value): bool
    {
        try {
            $recaptcha = RecaptchaEnterprise::assess($value);
        } catch (InvalidTokenException $exception) {
            $this->reason = $exception->reason;

            return false;
        }

        $validAction = true;
        $validInterval = true;

        if ($this->action) {
            $validAction = $recaptcha->validateAction($this->action);
        }

        if ($this->interval) {
            $validInterval = $recaptcha->validateCreationTime($this->interval);
        }

        return $recaptcha->validateScore() && $validAction && $validInterval;
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
        return __('recaptcha-enterprise::validation.recaptcha', [
            'reason' => $this->reason ? InvalidReason::name($this->reason) : 'Unknown reason',
        ]);
    }
}
