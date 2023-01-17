<?php

declare(strict_types=1);

use Oneduo\RecaptchaEnterprise\Facades\RecaptchaEnterprise;

it('handles a valid token', function () {
    RecaptchaEnterprise::fake();

    $result = RecaptchaEnterprise::handle(fake()->sentence)->isValid();

    expect($result)->toBeTrue();
});

it('handles an invalid token', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::setScore(0);
    RecaptchaEnterprise::setThreshold(1);

    $result = RecaptchaEnterprise::handle(fake()->sentence)->isValid();

    expect($result)->toBeFalse();
});

