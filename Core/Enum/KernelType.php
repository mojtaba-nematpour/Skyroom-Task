<?php

namespace Core\Enum;

use Core\Basic\Kernel;

/**
 * Kernel Types
 *
 * @see Kernel
 */
enum KernelType: int
{
    case Http = 0;

    case Command = 1;
}
