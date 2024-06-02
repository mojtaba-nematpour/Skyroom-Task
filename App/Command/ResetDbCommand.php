<?php

namespace App\Command;

use App\Models\Admin;
use App\Models\Token;
use App\Models\User;
use Core\Command\Command;
use Core\Http\Responses\IOResponse;

class ResetDbCommand extends Command
{
    public function run(): IOResponse
    {
        $this->connection->removeTable(Admin::class)
            ->removeTable(Token::class)
            ->removeTable(User::class);

        $this->connection->createTable(Admin::class)
            ->createTable(Token::class)
            ->createTable(User::class);

        return $this->io('Successfully Resets `Admin` && `Token` && `User` ');
    }
}
