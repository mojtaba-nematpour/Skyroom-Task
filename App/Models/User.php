<?php

namespace App\Models;

use Core\Database\Model;

class User extends Model
{
    public const Name = 'users';

    public const Schema = [
        'id' => 'INT NOT NULL AUTO_INCREMENT',
        'firstname' => 'VARCHAR(32) NOT NULL',
        'lastname' => 'VARCHAR(32) NOT NULL',
        'email' => 'VARCHAR(255) UNIQUE NOT NULL',
        'mobile' => 'VARCHAR(11) UNIQUE NOT NULL',
        '' => 'PRIMARY KEY (`id`)',
    ];

    private ?int $id = null;

    private string $firstname;

    private string $lastname;

    private string $email;

    private string $mobile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): User
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }
}
