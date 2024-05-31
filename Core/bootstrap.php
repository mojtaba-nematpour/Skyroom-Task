<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Basic\Kernel;
use Core\Basic\Router;
use Core\Http\Request;

$router = new Router();
$routes = require __DIR__ . '/../App/Config/Route.php';
$routes($router);

((new Kernel($router))->handle(new Request()))->send();
