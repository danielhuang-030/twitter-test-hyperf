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
namespace App\Request\Post;

use Hyperf\Validation\Request\FormRequest;

class StoreRequest extends FormRequest
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
            'content' => [
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
            'content' => '內容',
        ];
    }

    /**
     * messages.
     */
    public function messages(): array
    {
        return [
            'content.required' => ':attribute必填',
        ];
    }
}
