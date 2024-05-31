<?php

namespace Core\Http;

use Core\Http\Responses\JsonResponse;

class Controller
{
    protected ?Request $request = null;

    public function setRequest(Request $request): void
    {
        if ($this->request === null) {
            $this->request = $request;
        }
    }

    protected function json(mixed $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse();

        $response->setContent($data);
        $response->setStatusCode($status);

        return $response;
    }
}
