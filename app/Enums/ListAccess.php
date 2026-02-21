<?php

declare(strict_types=1);

namespace App\Enums;

enum ListAccess: int
{
    case PRIVATE = 1;
    case CAN_EDIT = 2;
    case INVITE = 4;
    case LINK = 8;
}
