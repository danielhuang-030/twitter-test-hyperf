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

use App\Exception\AppCustomException;
use App\Request\Follow\FollowingRequest;
use App\Request\Follow\UnfollowRequest;
use App\Service\FollowService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Qbhy\HyperfAuth\AuthManager;

/**
 * @AutoController
 * Class FollowController.
 */
class FollowController extends AbstractController
{
    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    /**
     * @Inject
     * @var FollowService
     */
    protected $service;

    /**
     * following.
     */
    public function following(FollowingRequest $request, int $id)
    {
        if (! $this->service->follow($id, data_get($this->auth->user(), 'id', 0))) {
            throw new AppCustomException('Error when following');
        }

        return [
            'message' => 'Successfully followed user!',
        ];
    }

    /**
     * unfollow.
     */
    public function unfollow(UnfollowRequest $request, int $id)
    {
        if (! $this->service->unfollow($id, data_get($this->auth->user(), 'id', 0))) {
            throw new AppCustomException('Error when unfollow');
        }

        return [
            'message' => 'Successfully unfollowed user!',
        ];
    }
}
