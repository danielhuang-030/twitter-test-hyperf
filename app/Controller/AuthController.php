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

use App\Request\Auth\LoginRequest;
use App\Request\Auth\SignupRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Qbhy\HyperfAuth\Annotation\Auth;
use Qbhy\HyperfAuth\AuthManager;

/**
 * @AutoController
 * Class AuthController.
 */
class AuthController extends AbstractController
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
     * signup.
     */
    public function signup(SignupRequest $request)
    {
        $user = $this->service->createUser([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ]);
        if (empty($user)) {
            return [
                'message' => 'Failed to create user!',
            ];
        }
        return [
            'message' => 'Successfully created user!',
        ];
    }

    /**
     * login.
     */
    public function login(LoginRequest $request)
    {
        $user = $this->service->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        if ($user === null) {
            return $this->response->withStatus(401)->json([
                'message' => 'Unauthorized',
            ]);
        }

        return array_merge($user->toArray(), [
            'token' => $this->auth->login($user),
        ]);
    }

    /**
     * @Auth("jwt")
     * @GetMapping(path="/logout")
     */
    public function logout()
    {
        $this->auth->logout();
        return 'logout ok';
    }

    /**
     * 使用 Auth 注解可以保证该方法必须通过某个 guard 的授权，支持同时传多个 guard，不传参数使用默认 guard.
     * @Auth("jwt")
     * @GetMapping(path="/info")
     * @return string
     */
    public function info()
    {
        $user = $this->auth->user();
        return 'hello ' . $user->name;
    }
}
