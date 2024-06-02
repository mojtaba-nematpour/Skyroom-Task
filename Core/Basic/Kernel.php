<?php

namespace Core\Basic;

use App\Models\Token;
use Core\Command\Command;
use Core\Database\Connection;
use Core\Enum\KernelType;
use Core\Http\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Responses\IOResponse;
use DateTimeImmutable;
use Exception;
use ReflectionMethod;

/**
 * @Kernel of the application
 */
class Kernel
{
    /**
     * @var Router Router service
     */
    protected Router $router;

    /**
     * @var Connection Database connection(PDO)
     */
    protected Connection $connection;

    /**
     * @param KernelType $kernelType specifying the kernel type (Http Request | Console Command)
     */
    public function __construct(private readonly KernelType $kernelType = KernelType::Http)
    {
        /**
         * Load database config for connection
         */
        $config = require __DIR__ . '/../../App/Config/Database.php';

        /**
         * Only on Http requests
         */
        if ($kernelType === KernelType::Http) {
            /**
             * Load routes
             */
            $routes = require __DIR__ . '/../../App/Config/Route.php';
            $this->router = new Router();
            $routes($this->router);
        }

        /**
         * Start db connection
         */
        $this->connection = new Connection($config);
    }

    public function find(string $command): IOResponse
    {
        $response = new IOResponse();
        try {
            $command = "App\\Command\\" . ucfirst($command) . "Command";

            /**
             * Check for command existence
             */
            if (!class_exists($command)) {
                return $response->setContent("Command $command not fund");
            }

            $parameters = [];

            /**
             * @var Command $command
             */
            $command = new $command();
            $reflectedMethod = new ReflectionMethod($command, 'run');
            $services = $reflectedMethod->getParameters();
            /**
             * Instantiate the services to inject in command run
             */
            foreach ($services as $service) {
                $serviceName = $service->getType()->getName();
                if (!class_exists($serviceName)) {
                    continue;
                }

                $parameters[] = new $serviceName();
            }

            /**
             * Load command basic needs
             */
            $command->setModelManager($this->connection);

            /**
             * Inject the services in controller method
             */
            return $command->run(...$parameters);
        } catch (Exception $e) {
            $response->setContent($e->getMessage());
        }

        return $response;
    }

    /**
     * Handles the Http requests
     *
     * @param Request $request current request
     *
     * @return Response controller response
     */
    public function handle(Request $request): Response
    {
        try {
            /**
             * Match the route and find the specified controller based on method
             */
            $matchedRoute = $this->router->match($request);

            $controller = $matchedRoute[0];
            $function = $matchedRoute[1];
            $guard = $matchedRoute[2];

            if ((bool)$guard === true) {
                /**
                 * @var Token[] $token
                 */
                $tokens = $this->connection->find(Token::class, [
                    'value' => $request->token
                ]);

                if (count($tokens) <= 0 || $tokens[0]['expiresAt'] > new DateTimeImmutable()) {
                    throw new Exception(json_encode(['errors' => "توکن یافت نشد یا منقضی شده"]), 403);
                }
            }

            /**
             * Check if the controller has proper function based on method
             *
             * @var Controller $controller
             */
            $controller = new $controller();
            if (!method_exists($controller, $function)) {
                $controller = get_class($controller);
                throw new Exception(json_encode(['errors' => "$controller does not respond to the $function action."]), 400);
            }

            $parameters = [];
            $reflectedMethod = new ReflectionMethod($controller, $function);
            $services = $reflectedMethod->getParameters();
            /**
             * Instantiate the services to inject in controller method
             */
            foreach ($services as $service) {
                $serviceName = $service->getType()->getName();
                $parameters[] = new $serviceName();
            }

            /**
             * Load controllers basic needs
             */
            $controller->setRequest($request)->setModelManager($this->connection);

            /**
             * Inject the services in controller method
             */
            return $controller->$function(...$parameters);
        } catch (Exception $e) {
            $response = new Response($e->getMessage(), 404);
        }

        return $response;
    }

    /**
     * @return KernelType
     */
    public function getType(): KernelType
    {
        return $this->kernelType;
    }
}
