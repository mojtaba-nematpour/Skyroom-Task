<?php

namespace Core\Basic;

use Core\Http\Request;
use Exception;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
        'DELETE' => []
    ];

    public function get($uri, $controller, $method): void
    {
        $this->routes['GET'][$uri] = [$controller, $method];
    }

    public function post($uri, $controller, $method): void
    {
        $this->routes['POST'][$uri] = [$controller, $method];
    }

    public function delete($uri, $controller, $method): void
    {
        $this->routes['DELETE'][$uri] = [$controller, $method];
    }

    /**
     * @throws Exception
     */
    public function match(Request $request)
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        if (array_key_exists($uri, $this->routes[$method])) {
            return $this->routes[$method][$uri];
        }

        throw new Exception('No route defined for this URI.');
    }
}
