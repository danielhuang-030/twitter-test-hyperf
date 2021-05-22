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

use App\Request\Traits\MergeRouteParams;
use Hyperf\Validation\Request\FormRequest;

class UpdateRequest extends FormRequest
{
    use MergeRouteParams;

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
            'id' => [
                'required',
                'exists:posts',
            ],
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
            'id' => '文章編號',
            'content' => '內容',
        ];
    }

    /**
     * messages.
     */
    public function messages(): array
    {
        return [
            'id.required' => ':attribute必填',
            'id.exists' => ':attribute不存在',
            'content.required' => ':attribute必填',
        ];
    }
}
