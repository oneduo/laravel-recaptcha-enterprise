<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Services;

use Carbon\CarbonInterval;
use Oneduo\RecaptchaEnterprise\Contracts\RecaptchaEnterpriseContract;

class FakeRecaptchaEnterpriseEnterprise implements RecaptchaEnterpriseContract
{
    public float $score = 1.0;

    public float $threshold = 0.0;

    public function handle(string $token): static
    {
        return $this;
    }

    public function isValid(?string $action = null, ?CarbonInterval $interval = null): bool
    {
        return $this->score >= $this->threshold;
    }

    public function setScore(float $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function setThreshold(float $threshold): static
    {
        $this->threshold = $threshold;

        return $this;
    }

    public function hasValidScore(): bool
    {
        return $this->score >= $this->threshold;
    }

    public function hasValidAction(string $action): bool
    {
        return true;
    }

    public function hasValidTimestamp(CarbonInterval $interval): bool
    {
        return true;
    }

    public function close(): void
    {
    }
}
