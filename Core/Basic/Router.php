<?php

namespace Core\Basic;

use Core\Http\Request;
use Exception;

/**
 * Core class to interact with requests and re-writing
 */
class Router
{
    /**
     * Route lists based on method
     *
     * @var array|array[]
     */
    protected array $routes = [
        'GET' => [],
        'POST' => [],
        'DELETE' => []
    ];

    /**
     * Define GET route
     *
     * @param string $uri
     * @param string $controller classname
     * @param string $method controller function
     *
     * @return void
     */
    public function get(string $uri, string $controller, string $method): void
    {
        $this->routes['GET'][$uri] = [$controller, $method];
    }

    /**
     * Define POST route
     *
     * @param string $uri
     * @param string $controller classname
     * @param string $method controller function
     *
     * @return void
     */
    public function post(string $uri, string $controller, string $method): void
    {
        $this->routes['POST'][$uri] = [$controller, $method];
    }

    /**
     * Define DELETE route
     *
     * @param string $uri
     * @param string $controller classname
     * @param string $method controller function
     *
     * @return void
     */
    public function delete(string $uri, string $controller, string $method): void
    {
        $this->routes['DELETE'][$uri] = [$controller, $method];
    }

    /**
     * Matches the re-wrote route to defined routes
     *
     * @param Request $request current request
     *
     * @return string[] containing controller name and function to call
     *
     * @throws Exception
     */
    public function match(Request $request): array
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        if (array_key_exists($uri, $this->routes[$method])) {
            return $this->routes[$method][$uri];
        }

        throw new Exception('No route defined for this URI.');
    }
}
