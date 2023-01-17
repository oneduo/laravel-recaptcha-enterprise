<?php

declare(strict_types=1);

return [
    'site_key' => env('RECAPTCHA_ENTERPRISE_SITE_KEY'),

    'use_credentials' => env('RECAPTCHA_ENTERPRISE_USE_CREDENTIALS', 'default'),

    'score_threshold' => env('RECAPTCHA_ENTERPRISE_SCORE_THRESHOLD', 0.5),

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
            'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/'.$email,
        ],
    ],
];
