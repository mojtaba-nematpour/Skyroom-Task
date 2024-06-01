<?php

namespace Core\Http;

use Core\Database\Connection;
use Core\Http\Responses\JsonResponse;

class Controller
{
    protected ?Request $request = null;

    protected ?Connection $connection = null;

    public function setRequest(Request $request): static
    {
        if ($this->request === null) {
            $this->request = $request;
        }

        return $this;
    }

    public function setModelManager(?Connection $con): static
    {
        if ($this->connection === null) {
            $this->connection = $con;
        }

        return $this;
    }

    protected function json(mixed $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse();

        $response->setContent($data);
        $response->setStatusCode($status);

        return $response;
    }
}
