<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Tests;

use Oneduo\RecaptchaEnterprise\RecaptchaEnterpriseServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            RecaptchaEnterpriseServiceProvider::class,
        ];
    }
}
