<?php

namespace Core\Http;

class Request
{
    protected string $uri;

    protected string $method;

    private array $attributes = [];

    private array $queries;

    private array $requests;

    private array $files;

    public function __construct()
    {
        $this->uri = (string)$_SERVER['REQUEST_URI'];
        $this->method = (string)$_SERVER['REQUEST_METHOD'];

        $this->queries = $_GET;
        $this->requests = $_POST;
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

    public function attrs(string $name = null): mixed
    {
        return $name === null ? $this->attributes : $this->attributes[$name] ?? null;
    }

    public function queries(string $name = null): mixed
    {
        return $name === null ? $this->queries : $this->queries[$name] ?? null;
    }

    public function requests(string $name = null): mixed
    {
        return $name === null ? $this->requests : $this->requests[$name] ?? null;
    }

    public function files(string $name = null): mixed
    {
        return $name === null ? $this->files : $this->files[$name] ?? null;
    }
}
