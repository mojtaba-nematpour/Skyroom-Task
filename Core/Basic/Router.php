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
     * @param bool $guard
     *
     * @return Router
     */
    public function get(string $uri, string $controller, string $method, bool $guard = true): static
    {
        $this->routes['GET'][$uri] = [$controller, $method, $guard];

        return $this;
    }

    /**
     * Define POST route
     *
     * @param string $uri
     * @param string $controller classname
     * @param string $method controller function
     * @param bool $guard
     *
     * @return Router
     */
    public function post(string $uri, string $controller, string $method, bool $guard = true): static
    {
        $this->routes['POST'][$uri] = [$controller, $method, $guard];

        return $this;
    }

    /**
     * Define DELETE route
     *
     * @param string $uri
     * @param string $controller classname
     * @param string $method controller function
     * @param bool $guard
     *
     * @return Router
     */
    public function delete(string $uri, string $controller, string $method, bool $guard = true): static
    {
        $this->routes['DELETE'][$uri] = [$controller, $method, $guard];

        return $this;
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

        foreach (array_keys($this->routes[$method]) as $route) {
            $pattern = str_replace('/', '\/', $route);
            /**
             * Convert URI into pattern
             */
            if (preg_match("/^$pattern$/m", $uri, $attributes)) {
                foreach ($attributes as $order => $value) {
                    $request->setAttr($order, $value);
                }

                return $this->routes[$method][$route];
            }
        }

        throw new Exception(json_encode([
            'errors' => Messages::get('errors', 'routeMissMatched')
        ]), 404);
    }
}
