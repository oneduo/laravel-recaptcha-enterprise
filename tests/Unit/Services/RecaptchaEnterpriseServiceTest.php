<?php

declare(strict_types=1);

use Oneduo\RecaptchaEnterprise\Facades\RecaptchaEnterprise;
use Oneduo\RecaptchaEnterprise\Services\RecaptchaEnterpriseService;

use function Pest\Laravel\partialMock;

it('returns a score for a valid token', function () {

    $token = fake()->word;

    $assessment = RecaptchaEnterprise::handle($token);
});
