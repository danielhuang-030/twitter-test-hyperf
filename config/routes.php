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

// Router::addGroup('/user', function () {
//     Router::post('', [UserController::class, 'store']);
// });
