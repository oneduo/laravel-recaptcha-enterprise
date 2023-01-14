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
