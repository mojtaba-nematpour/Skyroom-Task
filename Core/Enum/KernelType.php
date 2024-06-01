<?php

namespace Core\Enum;

enum KernelType: int
{
    case Http = 0;

    case Command = 1;
}
