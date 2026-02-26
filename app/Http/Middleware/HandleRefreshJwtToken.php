<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\JwtTokenType;

class HandleRefreshJwtToken extends AbstractHandleJwtToken
{
    protected JwtTokenType $jwtTokenType = JwtTokenType::Refresh;
}
