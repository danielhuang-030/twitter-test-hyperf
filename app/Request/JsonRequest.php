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
namespace App\Http\Requests;

use Hyperf\Contract\ValidatorInterface;
use Hyperf\Validation\Request\FormRequest;

abstract class JsonRequest extends FormRequest
{
    // /**
    //  * failed validation.
    //  */
    // protected function failedValidation(ValidatorInterface $validator)
    // {
    //     $errors = $validator->errors();
    //     throw new HttpResponseException(
    //         response()->json([
    //             'message' => $errors->first(),
    //         ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
    //     );
    // }
}
