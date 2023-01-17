<?php

namespace Oneduo\RecaptchaEnterprise\Contracts;

use Carbon\CarbonInterval;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException;

interface RecaptchaEnterpriseContract
{
    /**
     * @throws MissingPropertiesException
     * @throws InvalidTokenException
     * @throws \Google\ApiCore\ApiException
     */
    public function handle(string $token): static;

    public function hasValidScore(): bool;

    public function hasValidAction(string $action): bool;

    public function hasValidTimestamp(CarbonInterval $interval): bool;

    public function isValid(?string $action = null, ?CarbonInterval $interval = null): bool;

    public function close(): void;
}