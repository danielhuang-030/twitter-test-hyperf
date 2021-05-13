<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Request\Auth;

use Hyperf\Validation\Request\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * rules.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ];
    }

    /**
     * attributes.
     */
    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'password' => '密碼',
        ];
    }

    /**
     * messages.
     */
    public function messages(): array
    {
        return [
            'email.required' => ':attribute必填',
            'email.email' => ':attribute格式有誤',
            'password.required' => ':attribute必填',
        ];
    }
}
