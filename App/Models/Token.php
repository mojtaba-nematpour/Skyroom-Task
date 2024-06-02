<?php

namespace App\Models;

use Core\Database\Model;
use DateTimeImmutable;

class Token extends Model
{
    public const Name = 'tokens';

    public const Schema = [
        'id' => 'INT NOT NULL AUTO_INCREMENT',
        'user' => 'INT NOT NULL',
        'value' => 'VARCHAR(255) UNIQUE NOT NULL',
        'expiresAt' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
        '' => 'PRIMARY KEY (`id`)',
    ];

    private ?int $id = null;

    private int $user;

    private string $value;

    private DateTimeImmutable $expiresAt;

    public function __construct()
    {
        $this->expiresAt = new DateTimeImmutable('+15 minutes');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function setUser(int $user): Token
    {
        $this->user = $user;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeImmutable $expiresAt): Token
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
