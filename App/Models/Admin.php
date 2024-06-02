<?php

namespace App\Models;

use Core\Database\Model;
use Core\Interface\AuthenticateAble;

class Admin extends Model implements AuthenticateAble
{
    public const Name = 'admins';

    public const Schema = [
        'id' => 'INT NOT NULL AUTO_INCREMENT',
        'fullname' => 'VARCHAR(64) NOT NULL',
        'username' => 'VARCHAR(32) UNIQUE NOT NULL',
        'email' => 'VARCHAR(255) UNIQUE NOT NULL',
        'password' => 'VARCHAR(255) NOT NULL',
        '' => 'PRIMARY KEY (`id`)',
    ];

    private ?int $id = null;

    private string $fullname;

    private string $username;

    private string $email;

    private string $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): Admin
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Admin
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
