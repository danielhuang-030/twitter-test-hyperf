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
use App\Controller\UserController;
use Hyperf\HttpServer\Router\Router;
use Qbhy\HyperfAuth\AuthMiddleware;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::addServer('ws', function () {
    Router::get('/', 'App\Controller\WebSocketController');
});

// auth
Router::post('/signup', [AuthController::class, 'signup']);
Router::post('/login', [AuthController::class, 'login']);

// auth with middleware
Router::addGroup('', function () {
    // logout
    Router::get('/logout', [AuthController::class, 'logout']);

    // user
    Router::addGroup('/user', function () {
        Router::get('/{id:\d+}/info', [UserController::class, 'info']);
    });
}, [
    'middleware' => [
        AuthMiddleware::class,
    ],
]);
