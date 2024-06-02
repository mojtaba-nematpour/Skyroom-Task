<?php

namespace Core\Basic;

/**
 * Core class to interact with messages
 */
class Messages
{
    /**
     * @param string $domain message domain
     * @param string|null $id message id
     *
     * @return string|array|null On not providing $id returns the $domain messages - When message is not exists returns null
     */
    public static function get(string $domain, string $id = null): string|array|null
    {
        /**
         * Load app messages
         */
        $messages = require __DIR__ . '/../../App/Config/Messages.php';

        return ($id === null ? $messages[$domain] : ($messages[$domain][$id] ?? null));
    }
}
