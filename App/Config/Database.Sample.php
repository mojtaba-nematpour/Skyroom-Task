<?php

return [
    'name' => 'DB Name',
    'username' => 'Username',
    'password' => 'Password',
    'connection' => 'mysql:host=127.0.0.1',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
];
