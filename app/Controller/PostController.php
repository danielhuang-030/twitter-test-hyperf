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
namespace App\Controller;

use App\Service\PostService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Request;
use Qbhy\HyperfAuth\AuthManager;

/**
 * @AutoController
 * Class PostController.
 */
class PostController extends AbstractController
{
    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @Inject
     * @var PostService
     */
    protected $service;

    /**
     * store.
     */
    public function store(Request $request)
    {
        return $this->service->createPost($request->all(), data_get($this->auth->user(), 'id'));
    }
}
