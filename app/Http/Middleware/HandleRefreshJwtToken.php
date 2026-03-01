<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\JwtTokenType;

class HandleRefreshJwtToken extends AbstractHandleJwtToken
{
    protected function getJwtTokenTokenType(): JwtTokenType
    {
        return JwtTokenType::Refresh;
    }
}
