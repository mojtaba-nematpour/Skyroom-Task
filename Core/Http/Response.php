<?php

namespace Core\Http;

/**
 * Core class to interact with Kernel responses
 */
class Response
{
    protected array $headers = [];

    public function __construct(private string $content = '', private int $statusCode = 200)
    {
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setStatusCode($statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader($name, $value): static
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function sendHeaders(): static
    {
        foreach ($this->headers as $name => $value) {
            header("$name: $value", false, $this->statusCode);
        }

        return $this;
    }

    public function sendContent(): static
    {
        echo $this->content;

        return $this;
    }

    public function send(): static
    {
        $this->sendHeaders()->sendContent();

        return $this;
    }
}
