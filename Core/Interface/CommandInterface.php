<?php

namespace Core\Interface;

use Core\Http\Responses\IOResponse;

interface CommandInterface
{
    public function run(): IOResponse;
}
