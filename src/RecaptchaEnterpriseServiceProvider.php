<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RecaptchaEnterpriseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-recaptcha-enterprise')
            ->hasTranslations()
            ->hasConfigFile();
    }
}
