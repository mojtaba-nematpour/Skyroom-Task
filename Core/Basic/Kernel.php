<?php

namespace Core\Basic;

use Core\Database\Connection;
use Core\Enum\KernelType;
use Core\Http\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Exception;
use ReflectionMethod;

class Kernel
{
    protected Router $router;

    protected Connection $connection;

    public function __construct(KernelType $kernelType = KernelType::Http)
    {
        $config = require __DIR__ . '/../../App/Config/Database.php';

        if ($kernelType === KernelType::Http) {
            $routes = require __DIR__ . '/../../App/Config/Route.php';
            $this->router = new Router();
            $routes($this->router);
        }

        $this->connection = new Connection($config);
    }

    public function run()
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

            $controller->setRequest($request)->setModelManager($this->connection);

            return $controller->$method(...$parameters);
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), 404);
        }

        return $response;
    }
}
