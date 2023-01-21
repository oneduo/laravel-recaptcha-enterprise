<?php

declare(strict_types=1);

use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException;
use Oneduo\RecaptchaEnterprise\Facades\RecaptchaEnterprise;

it('handles a valid token', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::forceValid();

    $result = RecaptchaEnterprise::assess('valid-token')->isValid();

    expect($result)->toBeTrue();
});

it('handles an invalid token', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::setScore(0);
    RecaptchaEnterprise::setThreshold(1);

    $result = RecaptchaEnterprise::assess('invalid-token')->isValid();

    expect($result)->toBeFalse();
});

it('throws an exception when assessment properties are missing', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::shouldReceive('assess')->andThrow(MissingPropertiesException::forAssessment());

    $this->expectException(MissingPropertiesException::class);

    RecaptchaEnterprise::assess('valid')->isValid();
});

it('throws an exception when assessment is not valid', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::shouldReceive('assess')->andThrow(InvalidTokenException::forReason(InvalidReason::INVALID_REASON_UNSPECIFIED));

    $this->expectException(InvalidTokenException::class);

    RecaptchaEnterprise::assess('valid')->isValid();
});
