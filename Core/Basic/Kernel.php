<?php

namespace Core\Basic;

use Core\Http\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Exception;

class Kernel
{
    public function __construct(protected Router $router)
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $matchedRoute = $this->router->match($request);

            $controller = $matchedRoute[0];
            $method = $matchedRoute[1];

            /**
             * @var Controller $controller
             */
            $controller = new $controller();
            if (!method_exists($controller, $method)) {
                $controller = get_class($controller);
                throw new Exception("$controller does not respond to the $method action.");
            }

            $controller->setRequest($request);

            return $controller->$method();
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), 404);
        }

        return $response;
    }
}
