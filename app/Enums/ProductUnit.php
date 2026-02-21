<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductUnit: string
{
    case THING = 'thing';
    case PACKAGE = 'package';
    case KG = 'kg';
}
