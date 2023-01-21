# Google reCAPTCHA Enterprise for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/oneduo/laravel-recaptcha-enterprise.svg?style=flat-square)](https://packagist.org/packages/oneduo/laravel-recaptcha-enterprise)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/oneduo/laravel-recaptcha-enterprise/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/oneduo/laravel-recaptcha-enterprise/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/oneduo/laravel-recaptcha-enterprise/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/oneduo/laravel-recaptcha-enterprise/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/oneduo/laravel-recaptcha-enterprise.svg?style=flat-square)](https://packagist.org/packages/oneduo/laravel-recaptcha-enterprise)
[![codecov](https://codecov.io/github/oneduo/laravel-recaptcha-enterprise/branch/main/graph/badge.svg)](https://codecov.io/github/oneduo/laravel-recaptcha-enterprise)

Wrapper to use Google reCAPTCHA Enterprise with Laravel. Provides a handy validation rule to verify your token's score.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)


## Prerequisites

**TLDR;** You may want to follow
the [official documentation](https://cloud.google.com/recaptcha-enterprise/docs/set-up-google-cloud) to get started.

### 1. Enable the reCAPTCHA Enterprise API

On your Google Cloud console, go ahead and enable the reCAPTCHA Enterprise API.

### 2. Create a service account

Create a service account with the following roles:

- reCAPTCHA Enterprise Agent

### 3. Create a key

Create a key for your service account and download it as a JSON file.

### 4. Use your credentials

Use your credentials by setting the appropriate values in `config/recaptcha-enterprise.php` or by setting the
environment variables.

## Installation

You can install the package via composer:

```bash
composer require oneduo/laravel-recaptcha-enterprise
```

## Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-recaptcha-enterprise-config"
```

This is the contents of the published config file, you are required to set the variables accordingly:

```php
return [
    'site_key' => env('RECAPTCHA_ENTERPRISE_SITE_KEY'),

    'use_credentials' => env('RECAPTCHA_ENTERPRISE_USE_CREDENTIALS', 'default'),

    'credentials' => [
        'default' => [
            'type' => 'service_account',
            'project_id' => env('RECAPTCHA_ENTERPRISE_PROJECT_ID'),
            'private_key_id' => env('RECAPTCHA_ENTERPRISE_PRIVATE_KEY_ID'),
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

You may start using the reCAPTCHA validation rule by implementing the
available `Oneduo\RecaptchaEnterprise\Rules\Recaptcha` rule in your business logic, here's an example of a `FormRequest`
implementation:

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

### Configuring the threshold

When validating a token, you may want to set a threshold for the score. You can do so setting the `score_threshold`
config value:

```php
'score_threshold' => 0.7,
```

Default threshold is `0.5`

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
