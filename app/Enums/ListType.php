<?php

declare(strict_types=1);

namespace App\Enums;

enum ListType: string
{
    case SHOPPING = 'shopping';
    case TODO = 'todo';
    case TEMPLATE = 'template';
}
