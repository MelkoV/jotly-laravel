<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\JwtTokenType;
use App\Exceptions\JwtException;
use Illuminate\Http\Request;

class HandleRefreshJwtToken extends AbstractHandleJwtToken
{
    protected function getJwtTokenTokenType(): JwtTokenType
    {
        return JwtTokenType::Refresh;
    }

    /**
     * @throws JwtException
     */
    protected function getToken(Request $request): string
    {
        $cookieName = \Config::string('jwt.cookie.name');
        if (!$request->hasCookie($cookieName)) {
            throw new JwtException('Refresh token is required.');
        }
        return (string)$request->cookie($cookieName);
    }
}
