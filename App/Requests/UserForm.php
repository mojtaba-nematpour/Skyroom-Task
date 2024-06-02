<?php

namespace App\Requests;

use Core\Http\Form;

class UserForm extends Form
{
    public array $fields = [
        'firstname',
        'lastname',
        'email',
        'mobile',
    ];

    public array $validations = [
        'firstname' => [
            'minLength' => 3,
            'maxLength' => 32
        ],
        'lastname' => [
            'minLength' => 3,
            'maxLength' => 32
        ],
        'email' => [
            'email' => true,
            'minLength' => 5,
            'maxLength' => 255,
        ],
        'mobile' => [
            'minLength' => 11,
            'maxLength' => 11,
            'match' => '^(.*\d)$'
        ],
    ];

    public function filter(): static
    {
        foreach ($this->data as $datum => $value) {
            $this->data[$datum] = match ($datum) {
                'firstname', 'lastname' => ucfirst($value),
                'email' => strtolower($value),
                default => $value
            };
        }

        return $this;
    }
}
