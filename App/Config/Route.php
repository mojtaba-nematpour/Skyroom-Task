<?php

use App\Controllers\V1 as V1;
use Core\Basic\Router;

return function (Router $router) {
    $router->post('/v1/auth/login', V1\AuthController::class, 'login');
    $router->post('/v1/auth/register', V1\AuthController::class, 'register');

    return $router;
};
