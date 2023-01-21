<?php

namespace Oneduo\RecaptchaEnterprise\Contracts;

use Carbon\CarbonInterval;

interface RecaptchaContract
{
    /**
     * Assess the token against Google reCAPTCHA Enterprise
     *
     * @throws \Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException
     * @throws \Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException
     * @throws \Google\ApiCore\ApiException
     */
    public function assess(string $token): static;

    public function validateScore(): bool;

    public function validateAction(string $action): bool;

    public function validateCreationTime(CarbonInterval $interval): bool;

    public function isValid(?string $action = null, ?CarbonInterval $interval = null): bool;
}
