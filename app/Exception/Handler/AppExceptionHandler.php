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

use App\Exception\AppCustomException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        switch (true) {
            // auth
            case $throwable instanceof UnauthorizedException:
                /** @var UnauthorizedException $throwable */
                $statusCode = $throwable->getStatusCode();
                $message = 'Unauthorized';
                break;
            // validation
            case $throwable instanceof ValidationException:
                /** @var ValidationException $throwable */
                $statusCode = $throwable->status;
                $message = $throwable->validator->errors()->first();
                break;
            // custom
            case $throwable instanceof AppCustomException:
                /** @var AppCustomException $throwable */
                $statusCode = $throwable->getHttpStatusCode();
                $message = $throwable->getMessage();
                break;
            // others
            default:
                // logger
                $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
                $this->logger->error($throwable->getTraceAsString());

                $statusCode = 500;
                $message = 'Internal Server Error';
                break;
        }

        return $response->withHeader('content-type', 'application/json')
            ->withStatus($statusCode)
            ->withBody(new SwooleStream(json_encode([
                'message' => $message,
            ], JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
