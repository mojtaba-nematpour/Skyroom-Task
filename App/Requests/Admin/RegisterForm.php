<?php

namespace App\Requests\Admin;

use Core\Http\Form;

class RegisterForm extends Form
{
    public array $fields = [
        'fullname',
        'username',
        'email',
        'password',
        'confirmPassword',
    ];

    public array $guard = [
        'confirmPassword'
    ];

    public array $validations = [
        'fullname' => [
            'minLength' => 3,
            'maxLength' => 64
        ],
        'username' => [
            'minLength' => 3,
            'maxLength' => 64
        ],
        'email' => [
            'email' => true,
            'minLength' => 5,
            'maxLength' => 255,
        ],
        'password' => [
            'minLength' => 6,
            'maxLength' => 32,
            'match' => '^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$'
        ],
        'confirmPassword' => [
            'required' => false,
            'minLength' => 6,
            'maxLength' => 32,
            'equal' => 'password',
            'match' => '^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$'
        ],
    ];

    public function filter(): static
    {
        foreach ($this->data as $datum => $value)  {
            $this->data[$datum] = match ($datum) {
                'fullname' => ucfirst($value),
                'username', 'email' => strtolower($value),
                'password' => sha1($value),
                default => $value
            };
        }

        return $this;
    }
}
