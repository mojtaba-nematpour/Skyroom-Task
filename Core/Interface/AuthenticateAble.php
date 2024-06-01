<?php

namespace Core\Interface;

interface AuthenticateAble
{
    public function getUsername(): string;

    public function setUsername(string $username): static;

    public function getPassword(): string;

    public function setPassword(string $password): static;

}
