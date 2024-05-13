<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Mocks;

use Carbon\CarbonInterval;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties;
use Illuminate\Support\Carbon;
use Oneduo\RecaptchaEnterprise\Contracts\RecaptchaContract;
use RuntimeException;

/**
 * @codeCoverageIgnore
 */
class FakeRecaptchaEnterprise implements RecaptchaContract
{
    public ?float $threshold;

    public function __construct(
        public ?bool $alwaysValid = null,
        public ?float $score = null,
        public ?TokenProperties $properties = null,
    ) {
        $this->threshold = config('recaptcha-enterprise.score_threshold');
    }

    public function assess(string $token): static
    {
        return $this;
    }

    public function validateScore(): bool
    {
        // if no threshold is set, we assume it passes
        if ($this->threshold === null) {
            return true;
        }

        // if no score is set, we assume it fails
        if ($this->score === null) {
            return false;
        }

        // check if the score is higher than the threshold
        return $this->score >= $this->threshold;
    }

    public function validateAction(string $action): bool
    {
        return $this->properties->getAction() === $action;
    }

    public function validateCreationTime(CarbonInterval $interval): bool
    {
        $timestamp = $this->properties->getCreateTime()?->getSeconds();

        return Carbon::parse($timestamp)->lessThanOrEqualTo(now()->sub($interval));
    }

    public function isValid(?string $action = null, ?CarbonInterval $interval = null): bool
    {
        if (is_bool($this->alwaysValid)) {
            return $this->alwaysValid;
        }

        $valid = $this->validateScore();

        if ($action) {
            $valid = $valid && $this->validateAction($action);
        }

        if ($interval) {
            $valid = $valid && $this->validateCreationTime($interval);
        }

        return $valid;
    }

    public function setScore(float $score): static
    {
        if (! app()->runningUnitTests()) {
            throw new RuntimeException('This method is only available in tests');
        }

        $this->score = $score;

        return $this;
    }

    public function setThreshold(float $threshold): static
    {
        if (! app()->runningUnitTests()) {
            throw new RuntimeException('This method is only available in tests');
        }

        $this->threshold = $threshold;

        return $this;
    }

    public function setProperties(TokenProperties $properties): static
    {
        if (! app()->runningUnitTests()) {
            throw new RuntimeException('This method is only available in tests');
        }

        $this->properties = $properties;

        return $this;
    }

    public function forceValid(bool $value = true): static
    {
        if (! app()->runningUnitTests()) {
            throw new RuntimeException('This method is only available in tests');
        }

        $this->alwaysValid = $value;

        return $this;
    }
}
