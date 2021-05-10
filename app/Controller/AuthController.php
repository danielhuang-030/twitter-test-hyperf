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
use Hyperf\HttpServer\Contract\ResponseInterface;
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
    public function login(LoginRequest $request, ResponseInterface $response)
    {
        $user = $this->service->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        if ($user === null) {
            return $response->json([
                'message' => 'Unauthorized',
            ])->withStatus(401);
        }

        return array_merge($user->toArray(), [
            'token' => $this->auth->login($user),
        ]);
    }

    /**
     * logout.
     */
    public function logout()
    {
        $this->auth->logout();

        return [
            'message' => 'Logged out',
        ];
    }
}
