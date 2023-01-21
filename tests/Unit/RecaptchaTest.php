<?php

declare(strict_types=1);

use Carbon\CarbonInterval;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Google\Protobuf\Timestamp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Facades\RecaptchaEnterprise;
use Oneduo\RecaptchaEnterprise\Rules\Recaptcha;

it('passes validation for a valid token', function () {
    RecaptchaEnterprise::fake();
    RecaptchaEnterprise::setScore(1);
    RecaptchaEnterprise::setThreshold(0.5);

    $rule = new Recaptcha();

    $result = $rule->passes('recaptcha', 'valid-token');

    expect($result)->toBeTrue();
});

it('fails validation for an invalid token', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::shouldReceive('assess')->andThrow(InvalidTokenException::forReason(InvalidReason::EXPIRED));

    $rule = new Recaptcha();

    $result = $rule->passes('recaptcha', 'valid-token');

    expect($result)->toBeFalse();
});

it('validates token and action', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::setProperties((new TokenProperties())->setAction('test'));

    $rule = new Recaptcha(action: 'test-action');

    $result = $rule->passes('recaptcha', 'valid-token');

    expect($result)->toBeFalse();
});

it('validates token and creation time', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::setProperties((new TokenProperties())->setCreateTime(new Timestamp([
        'seconds' => now()->timestamp,
    ])));

    $rule = new Recaptcha(interval: CarbonInterval::minutes(5));

    $result = $rule->passes('recaptcha', 'valid-token');

    expect($result)->toBeFalse();
});

it('validates token, action and creation time', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::setProperties(
        (new TokenProperties())
            ->setAction('test-action')
            ->setCreateTime(new Timestamp([
                'seconds' => now()->timestamp,
            ]))
    );

    $rule = (new Recaptcha())
        ->action('test-action')
        ->validity(CarbonInterval::minutes(5));

    $result = $rule->passes('recaptcha', 'valid-token');

    expect($result)->toBeFalse();
});

it('validates with rule and returns proper message', function () {
    RecaptchaEnterprise::fake();

    RecaptchaEnterprise::shouldReceive('assess')->andThrow(InvalidTokenException::forReason(InvalidReason::EXPIRED));

    $validator = Validator::make([
        'token' => 'invalid-token',
    ], [
        'token' => [new Recaptcha()],
    ]);

    $this->expectException(ValidationException::class);

    $this->expectExceptionMessage(__('validation.recaptcha'));

    $validator->validate();
});
