<?php

use App\Controllers\V1 as V1;
use Core\Basic\Router;

return function (Router $router) {
    $router->post('/v1/auth/login', V1\AuthController::class, 'login')
        ->post('/v1/auth/register', V1\AuthController::class, 'register');

    $router->get('/v1/users/find/(\d.*|[a-zA-z].*)', V1\UserController::class, 'search')
        ->get('/v1/users', V1\UserController::class, 'index')
        ->post('/v1/users', V1\UserController::class, 'new');

    $router->get('/v1/users/(\d.*)', V1\UserController::class, 'view')
        ->post('/v1/users/(\d.*)', V1\UserController::class, 'edit')
        ->delete('/v1/users/(\d.*)', V1\UserController::class, 'destroy');

    return $router;
};
