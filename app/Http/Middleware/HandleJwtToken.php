<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\JwtTokenType;

class HandleJwtToken extends AbstractHandleJwtToken
{
    protected JwtTokenType $jwtTokenType = JwtTokenType::Temporary;
}
