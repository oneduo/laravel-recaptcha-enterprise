# This is my package laravel-recaptcha-enterprise

[![Latest Version on Packagist](https://img.shields.io/packagist/v/oneduo/laravel-recaptcha-enterprise.svg?style=flat-square)](https://packagist.org/packages/oneduo/laravel-recaptcha-enterprise)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/oneduo/laravel-recaptcha-enterprise/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/oneduo/laravel-recaptcha-enterprise/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/oneduo/laravel-recaptcha-enterprise/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/oneduo/laravel-recaptcha-enterprise/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/oneduo/laravel-recaptcha-enterprise.svg?style=flat-square)](https://packagist.org/packages/oneduo/laravel-recaptcha-enterprise)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require oneduo/laravel-recaptcha-enterprise
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-recaptcha-enterprise-config"
```

This is the contents of the published config file:

```php
return [
    'project_name' => env('RECAPTCHA_ENTERPRISE_PROJECT_NAME'),

    'site_key' => env('RECAPTCHA_ENTERPRISE_SITE_KEY'),

    'use_credentials' => env('RECAPTCHA_ENTERPRISE_USE_CREDENTIALS', 'default'),

    'credentials' => [
        'default' => [
            'type' => 'service_account',
            'project_id' => env('RECAPTCHA_ENTERPRISE_PROJECT_ID'),
            'private_key_id' => env('RECAPTCHA_ENTERPRISE_KEY_ID'),
            'private_key' => env('RECAPTCHA_ENTERPRISE_PRIVATE_KEY'),
            'client_email' => $email = env('RECAPTCHA_ENTERPRISE_CLIENT_EMAIL'),
            'client_id' => env('RECAPTCHA_ENTERPRISE_CLIENT_ID'),
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://accounts.google.com/o/oauth2/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/' . $email,
        ],
    ],
];
```

## Usage

@todo: add more info

```php
<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Oneduo\RecaptchaEnterprise\Rules\Recaptcha;

class TestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'g-recaptcha-response' => ['required', new Recaptcha()],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Charaf Rezrazi](https://github.com/oneduo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
