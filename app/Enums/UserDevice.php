<?php

declare(strict_types=1);

namespace App\Enums;

enum UserDevice: string
{
    case WEB = 'web';
    case ANDROID = 'android';
    case IOS = 'ios';
}
