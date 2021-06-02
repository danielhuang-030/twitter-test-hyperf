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
namespace App\Middleware\Post;

use App\Exception\AppCustomException;
use App\Service\PostService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetPostMiddleware implements MiddlewareInterface
{
    /**
     * request name post.
     *
     * @var string
     */
    const REQUEST_NAME_POST = 'post';

    /**
     * @Inject
     * @var PostService
     */
    protected $service;

    /**
     * @Inject
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * construct.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * process.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $post = $this->service->getPost((int) $this->request->route('id', 0));
        if (empty($post)) {
            throw (new AppCustomException('Post does not exist'))->setHttpStatusCode(403);
        }
        // $this->logger->debug(\var_export($request->all(), true));
        $request = $request->withAttribute(static::REQUEST_NAME_POST, $post);

        return $handler->handle($request);
    }
}
