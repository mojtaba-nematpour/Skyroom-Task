<?php

namespace Core\Http;

class Request
{
    protected string $uri;

    protected string $method;

    private array $attributes = [];

    private array $query;

    private array $request;

    private array $files;

    public function __construct()
    {
        $this->uri = (string)$_SERVER['REQUEST_URI'];
        $this->method = (string)$_SERVER['REQUEST_METHOD'];

        $this->query = $_GET;
        $this->request = $_POST;
        $this->files = $_FILES;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function attr(string $name): bool|int|string|null
    {
        return $this->attributes[$name] ?? null;
    }

    public function query(string $name): bool|int|string|null
    {
        return $this->query[$name] ?? null;
    }

    public function request(string $name): bool|int|string|null
    {
        return $this->request[$name] ?? null;
    }

    public function file(string $name): array|null
    {
        return $this->files[$name] ?? null;
    }
}
