<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Basic\Kernel;
use Core\Http\Request;

((new Kernel())->handle(new Request()))->send();
