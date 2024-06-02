<?php

namespace App\Requests\Admin;

use Core\Http\Form;

class TokenForm extends Form
{
    public array $fields = [
        'user'
    ];
}
