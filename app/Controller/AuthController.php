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

use App\Model\User;
use App\Request\Auth\SignupRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
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
     * @GetMapping(path="/login")
     * @return array
     */
    public function login()
    {
        /* @var User $user */
        // $user = User::query()->firstOrCreate([
        //     'name' => 'test001',
        //     'username' => 'test001',
        //     'password' => 'ssssssss', // Hash::make('aaaaaaaa'),
        //     'email' => 'test001@test.com',
        // ]);
        $user = User::query()->where('id', 1)->first();
        return [
            'token' => $this->auth->login($user),
        ];
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