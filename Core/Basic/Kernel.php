<?php

namespace Core\Basic;

use Core\Http\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Exception;
use ReflectionMethod;

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

            $parameters = [];
            $reflectedMethod = new ReflectionMethod($controller, $method);
            $services = $reflectedMethod->getParameters();
            foreach ($services as $service) {
                $serviceName = $service->getType()->getName();
                if (!class_exists($serviceName)) {
                    continue;
                }

                $parameters[] = new $serviceName();
            }

            $controller->setRequest($request);

            return $controller->$method(...$parameters);
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), 404);
        }

        return $response;
    }
}
