<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Oneduo\RecaptchaEnterprise\Contracts\RecaptchaContract;
use Oneduo\RecaptchaEnterprise\Services\RecaptchaService;
use Oneduo\RecaptchaEnterprise\Tests\Mocks\FakeRecaptchaEnterprise;

/**
 * @method static static setThreshold(float $threshold)
 * @method static static setScore(float $score)
 * @method static static setProperties(\Google\Cloud\RecaptchaEnterprise\V1\TokenProperties $properties)
 * @method static static forceValid(bool $value = true)
 *
 * @see RecaptchaService
 */
class RecaptchaEnterprise extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RecaptchaService::class;
    }

    /**
     * @throws \Google\ApiCore\ApiException
     * @throws \Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException
     * @throws \Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException
     */
    public static function assess(string $token): RecaptchaContract
    {
        return app(RecaptchaService::class)->assess($token);
    }

    public static function fake(?bool $alwaysValid = null, ?Closure $callback = null): RecaptchaContract
    {
        return tap(static::getFacadeRoot(), function (RecaptchaService $fake) use ($alwaysValid, $callback) {
            static::swap(is_callable($callback) ? $callback($fake) : new FakeRecaptchaEnterprise($alwaysValid));
        });
    }
}
