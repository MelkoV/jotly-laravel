<?php

namespace App\Enums;

enum JwtTokenType: string
{
    case Temporary = 'temporary';
    case Permanent = 'permanent';
}
