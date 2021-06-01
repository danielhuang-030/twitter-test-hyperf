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
use App\Controller\AuthController;
use App\Controller\FollowController;
use App\Controller\PostController;
use App\Controller\UserController;
use App\Middleware\User\GetUserMiddleware;
use Hyperf\HttpServer\Router\Router;
use Qbhy\HyperfAuth\AuthMiddleware;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::addServer('ws', function () {
    Router::get('/', 'App\Controller\WebSocketController');
});

// api
Router::addGroup('/api', function () {
    // auth
    Router::post('/signup', [AuthController::class, 'signup']);
    Router::post('/login', [AuthController::class, 'login']);

    // auth with AuthMiddleware
    Router::addGroup('', function () {
        // logout
        Router::get('/logout', [AuthController::class, 'logout']);

        // user, follow with GetUserMiddleware
        Router::addGroup('', function () {
            // user
            Router::addGroup('/users', function () {
                Router::get('/{id:\d+}/info', [UserController::class, 'info']);
                Router::get('/{id:\d+}/following', [UserController::class, 'following']);
                Router::get('/{id:\d+}/followers', [UserController::class, 'followers']);
                Router::get('/{id:\d+}/liked_posts', [UserController::class, 'likedPosts']);
            });

            // follow
            Router::addGroup('/following', function () {
                Router::patch('/{id:\d+}', [FollowController::class, 'following']);
                Router::delete('/{id:\d+}', [FollowController::class, 'unfollow']);
            });
        }, [
            'middleware' => [
                GetUserMiddleware::class,
            ],
        ]);

        // post
        Router::addGroup('/posts', function () {
            Router::get('/{id:\d+}', [PostController::class, 'show']);
            Router::post('/', [PostController::class, 'store']);
            Router::put('/{id:\d+}', [PostController::class, 'update']);
            Router::delete('/{id:\d+}', [PostController::class, 'destroy']);
            Router::patch('/{id:\d+}/like', [PostController::class, 'like']);
            Router::delete('/{id:\d+}/like', [PostController::class, 'dislike']);
        });
    }, [
        'middleware' => [
            AuthMiddleware::class,
        ],
    ]);
});
