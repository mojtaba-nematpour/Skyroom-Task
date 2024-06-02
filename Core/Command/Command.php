<?php

namespace Core\Command;

use Core\Database\Connection;
use Core\Http\Responses\IOResponse;
use Core\Interface\CommandInterface;

/**
 * Core command interface
 */
abstract class Command implements CommandInterface
{
    /**
     * Database connection
     *
     * @var Connection|null
     */
    protected ?Connection $connection = null;

    /**
     * Sets the ModelManager(Database connection) for command classes
     *
     * @param Connection $connection
     *
     * @return $this
     */
    public function setModelManager(Connection $connection): static
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Handles command input and outputs
     *
     * @param string $message
     *
     * @return IOResponse
     */
    protected function io(string $message): IOResponse
    {
        $response = new IOResponse();

        $response->setContent($message);

        return $response;
    }
}
