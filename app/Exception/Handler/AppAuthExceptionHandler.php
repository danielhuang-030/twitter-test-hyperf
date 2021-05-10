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
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\AuthExceptionHandler;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Throwable;

class AppAuthExceptionHandler extends AuthExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof UnauthorizedException) {
            $this->stopPropagation();

            if (! $response->hasHeader('content-type')) {
                $response = $response->withAddedHeader('content-type', 'application/json; charset=utf-8');
            }

            return $response->withStatus($throwable->getStatusCode())->withBody(new SwooleStream(json_encode([
                'message' => 'Unauthorized',
            ], JSON_UNESCAPED_UNICODE)));
        }

        return $response;
    }
}
