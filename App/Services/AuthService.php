<?php

namespace App\Services;

use App\Models\Token;
use App\Requests\Admin\TokenForm;
use Core\Database\Connection;
use Core\Interface\AuthenticateAble;
use DateTimeImmutable;

class AuthService
{
    private ?Connection $connection = null;

    public function authenticate(array|AuthenticateAble $authenticateAble): string
    {
        $id = $authenticateAble instanceof AuthenticateAble ? $authenticateAble->getId() : $authenticateAble['id'] ?? 0;
        $this->connection->remove(Token::class, [
            'user' => $id
        ]);

        $tokenRequest = new TokenForm();
        $tokenRequest->setData([
            'user' => $id,
            'value' => $token = md5(random_int(0, 9999999)),
            'expiresAt' => new DateTimeImmutable('+15 minutes')
        ]);
        $this->connection->save(Token::class, $tokenRequest);

        return $token;
    }

    public function setConnection(?Connection $connection): static
    {
        $this->connection = $connection;

        return $this;
    }
}
