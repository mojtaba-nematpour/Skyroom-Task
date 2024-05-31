<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Basic\Router;
use Core\Http\Request;
use Core\Basic\Kernel;

$router = new Router();
$routes = require __DIR__ . '/../App/Config/Route.php';
$routes($router);

((new Kernel($router))->handle(new Request()))->send();
