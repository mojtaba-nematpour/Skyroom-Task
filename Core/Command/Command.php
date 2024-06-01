<?php

namespace Core\Command;

use Core\Database\Connection;
use Core\Http\Responses\IOResponse;
use Core\Interface\CommandInterface;

abstract class Command implements CommandInterface
{
    protected ?Connection $connection = null;

    public function setModelManager(Connection $connection): static
    {
        $this->connection = $connection;

        return $this;
    }

    protected function io(string $message): IOResponse
    {
        $response = new IOResponse();

        $response->setContent($message);

        return $response;
    }
}
