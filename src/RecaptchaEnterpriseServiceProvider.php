<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise;

use Oneduo\RecaptchaEnterprise\Contracts\RecaptchaContract;
use Oneduo\RecaptchaEnterprise\Services\RecaptchaService;
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

    public function packageBooted(): void
    {
        $this->app->singleton(RecaptchaContract::class, fn () => new RecaptchaService());
    }
}
