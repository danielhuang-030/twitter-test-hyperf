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

use App\Request\Post\LikeRequest;
use App\Request\Post\ShowRequest;
use App\Request\Post\StoreRequest;
use App\Request\Post\UpdateRequest;
use App\Service\PostService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\ResponseInterface;
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
    public function store(StoreRequest $request, ResponseInterface $response)
    {
        $post = $this->service->createPost($request->all(), data_get($this->auth->user(), 'id', 0));
        if (empty($post)) {
            return $response->json([
                'message' => 'error',
            ])->withStatus(400);
        }

        return $post;
    }

    /**
     * show.
     */
    public function show(ShowRequest $request, int $id)
    {
        return $this->service->getPost($id);
    }

    /**
     * update.
     */
    public function update(UpdateRequest $request, ResponseInterface $response, int $id)
    {
        $post = $this->service->updatePost($request->all(), $id, data_get($this->auth->user(), 'id', 0));
        if (empty($post)) {
            return $response->json([
                'message' => 'error',
            ])->withStatus(400);
        }

        return $post;
    }

    /**
     * destroy.
     */
    public function destroy(ResponseInterface $response, int $id)
    {
        if (! $this->service->deletePost($id, data_get($this->auth->user(), 'id', 0))) {
            return $response->json([
                'message' => 'error',
            ])->withStatus(400);
        }

        return $response->json([
            'message' => 'Successfully deleted post!',
        ]);
    }

    /**
     * like.
     */
    public function like(LikeRequest $request, ResponseInterface $response, int $id)
    {
        if (! $this->service->likePost($id, data_get($this->auth->user(), 'id', 0))) {
            return $response->json([
                'message' => 'error',
            ])->withStatus(400);
        }

        return $response->json([
            'message' => 'Successfully liked post!',
        ]);
    }
}
