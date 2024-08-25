<?php

namespace Spatie\LaravelPasskeys\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateUsingPasskeysRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answer' => ['required', 'json'],
            'redirect' => ['url'],
        ];
    }
}
