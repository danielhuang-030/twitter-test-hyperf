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

use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Request;
use Qbhy\HyperfAuth\AuthManager;

/**
 * @AutoController
 * Class UserController.
 */
class UserController extends AbstractController
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
     * @var UserService
     */
    protected $service;

    /**
     * info.
     */
    public function info(Request $request, int $id)
    {
        // $user = $this->auth->user();
        $user = $this->service->getUser($id);
        if (empty($user)) {
            return [
                'message' => 'Failed to get user!',
            ];
        }

        return $user->toArray();
    }
}
