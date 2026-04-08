<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\JwtTokenType;
use App\Exceptions\JwtException;
use Illuminate\Http\Request;

class HandleRefreshJwtToken extends AbstractHandleJwtToken
{
    const string COOKIE_NAME = 'refresh_token';

    protected function getJwtTokenTokenType(): JwtTokenType
    {
        return JwtTokenType::Refresh;
    }

    /**
     * @throws JwtException
     */
    protected function getToken(Request $request): string
    {
        if (!$request->hasCookie(self::COOKIE_NAME)) {
            throw new JwtException('Refresh token is required.');
        }
        return (string)$request->cookie(self::COOKIE_NAME);
    }
}
