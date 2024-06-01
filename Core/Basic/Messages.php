<?php

namespace Core\Basic;

class Messages
{
    public static function get(string $domain, string $id = null): string|array|null
    {
        $messages = require __DIR__ . '/../../App/Config/Messages.php';

        return ($id === null ? $messages[$domain] : ($messages[$domain][$id] ?? null));
    }
}
