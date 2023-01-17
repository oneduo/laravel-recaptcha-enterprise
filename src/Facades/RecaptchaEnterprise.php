<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Facades;

use Closure;
use Google\ApiCore\ApiException;
use Illuminate\Support\Facades\Facade;
use Oneduo\RecaptchaEnterprise\Contracts\RecaptchaEnterpriseContract;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException;
use Oneduo\RecaptchaEnterprise\Services\FakeRecaptchaEnterpriseEnterprise;
use Oneduo\RecaptchaEnterprise\Services\RecaptchaEnterpriseService;

/**
 * @method static static setThreshold(float $threshold)
 * @method static static setScore(float $threshold)
 *
 * @see RecaptchaEnterpriseService
 */
class RecaptchaEnterprise extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RecaptchaEnterpriseService::class;
    }

    /**
     * @throws ApiException
     * @throws InvalidTokenException
     * @throws MissingPropertiesException
     */
    public static function handle(string $token): RecaptchaEnterpriseContract
    {
        return app(RecaptchaEnterpriseService::class)->handle($token);
    }

    public static function fake(?Closure $callback = null)
    {
        return tap(static::getFacadeRoot(), function (RecaptchaEnterpriseService $fake) use ($callback) {
            static::swap(is_callable($callback) ? $callback($fake) : new FakeRecaptchaEnterpriseEnterprise());
        });
    }
}
