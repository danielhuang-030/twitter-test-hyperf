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
namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Hyperf\Utils\Context;

class AppCustomException extends ServerException
{
    /**
     * @var string
     */
    const CONTEXT_NAME_HTTP_STATUS_CODE = 'HTTP_STATUS_CODE';

    /**
     * Get the value of httpStatusCode.
     */
    public function getHttpStatusCode(): int
    {
        return Context::get(static::getContextName(static::CONTEXT_NAME_HTTP_STATUS_CODE));
    }

    /**
     * Set the value of httpStatusCode.
     *
     * @return self
     */
    public function setHttpStatusCode(int $httpStatusCode)
    {
        Context::set(static::getContextName(static::CONTEXT_NAME_HTTP_STATUS_CODE), $httpStatusCode);

        return $this;
    }

    /**
     * get context name.
     */
    protected static function getContextName(string $name): string
    {
        return sprintf('%s::%s', __CLASS__, $name);
    }
}
