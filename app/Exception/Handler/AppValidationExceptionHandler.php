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
namespace App\Exception\Handler;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppValidationExceptionHandler extends ValidationExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        /* @var \Hyperf\Validation\ValidationException $throwable */
        if (! $response->hasHeader('content-type')) {
            $response = $response->withAddedHeader('content-type', 'application/json; charset=utf-8');
        }
        return $response->withStatus($throwable->status)->withBody(new SwooleStream(json_encode([
            'message' => $throwable->validator->errors()->first(),
        ], JSON_UNESCAPED_UNICODE)));
    }
}
