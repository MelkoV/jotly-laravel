<?php

declare(strict_types=1);

namespace App\Enums;

enum ListFilterTemplate: string
{
    /** Список покупок */
    case Template = 'template';
    /** Список дел/задач */
    case Worksheet = 'worksheet';
}
