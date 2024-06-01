<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Basic\Kernel;
use Core\Enum\KernelType;
use Core\Http\Request;

$kernel = new Kernel(isset($argv[1])  ? KernelType::Command: KernelType::Http);

$response = match ($kernel->getType()) {
    KernelType::Http => $kernel->handle(new Request()),
    KernelType::Command => $kernel->find($argv[1])
};

$response->send();
