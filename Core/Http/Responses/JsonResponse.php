<?php

namespace Core\Http\Responses;

use Core\Http\Response;

class JsonResponse extends Response
{
    protected array $headers = [
        'Content-type' => 'application/json'
    ];

    public function setContent($content): void
    {
        parent::setContent(json_encode($content));
    }
}
