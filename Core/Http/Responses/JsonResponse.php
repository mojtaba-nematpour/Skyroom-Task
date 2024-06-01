<?php

namespace Core\Http\Responses;

use Core\Http\Response;

class JsonResponse extends Response
{
    protected array $headers = [
        'Content-type' => 'application/json'
    ];

    public function setContent($content): static
    {
        return parent::setContent(json_encode($content));
    }
}
