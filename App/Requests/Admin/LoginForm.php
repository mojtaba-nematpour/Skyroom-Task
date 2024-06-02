<?php

namespace App\Requests\Admin;

use Core\Http\Form;

class LoginForm extends Form
{
    public array $fields = [
        'username',
        'password'
    ];

    public array $validations = [
        'username' => [
            'minLength' => 3,
            'maxLength' => 64
        ],
        'password' => [
            'minLength' => 6,
            'maxLength' => 32,
            'match' => '^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$',
        ]
    ];

    public function filter(): static
    {
        foreach ($this->data as $datum => $value)  {
            $this->data[$datum] = match ($datum) {
                'username' => strtolower($value),
                'password' => sha1($value),
                default => $value
            };
        }

        return $this;
    }
}
