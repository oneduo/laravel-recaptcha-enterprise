<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Facades;

use Closure;
use Google\ApiCore\ApiException;
use Illuminate\Support\Facades\Facade;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException;
use Oneduo\RecaptchaEnterprise\Services\RecaptchaEnterpriseService;

/**
 * @see RecaptchaEnterpriseService
 */
class RecaptchaEnterprise extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RecaptchaEnterpriseService::class;
    }

    /**
     * @param  string  $token
     * @return RecaptchaEnterpriseService
     *
     * @throws ApiException
     * @throws InvalidTokenException
     * @throws MissingPropertiesException
     */
    public static function handle(string $token): RecaptchaEnterpriseService
    {
        return app(RecaptchaEnterpriseService::class)->handle($token);
    }

    public static function fake(?Closure $callback = null)
    {
        return tap(static::getFacadeRoot(), function (RecaptchaEnterpriseService $fake) use ($callback) {
            static::swap($fake->fake($callback));
        });
    }
}
