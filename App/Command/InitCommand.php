<?php

namespace App\Command;

use App\Models\Admin;
use App\Models\Token;
use App\Models\User;
use Core\Command\Command;
use Core\Http\Responses\IOResponse;

class InitCommand extends Command
{
    public function run(): IOResponse
    {
        $this->connection->createTable('admins', Admin::Table)
            ->createTable('tokens', Token::Table)
            ->createTable('users', User::Table);

        return $this->io('Successfully Created `Admin` && `Token` && `User` ');
    }
}
