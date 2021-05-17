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

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
                'unique:users',
            ],
            'name' => [
                'required',
            ],
            'password' => [
                'required',
                'same:password_confirmation',
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
            'name' => '名稱',
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
            'email.unique' => ':attribute已存在',
            'name.required' => ':attribute必填',
            'password.required' => ':attribute必填',
            'password.same' => '二次輸入的:attribute不一致',
        ];
    }
}
