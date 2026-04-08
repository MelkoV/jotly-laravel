<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\JwtTokenType;
use App\Exceptions\JwtException;
use Illuminate\Http\Request;

class HandleJwtToken extends AbstractHandleJwtToken
{
    protected function getJwtTokenTokenType(): JwtTokenType
    {
        return JwtTokenType::Temporary;
    }

    /**
     * @throws JwtException
     */
    protected function getToken(Request $request): string
    {
        $bearerToken = $request->bearerToken();
        if (!$bearerToken) {
            throw new JwtException('Bearer token is required.');
        }
        return $bearerToken;
    }
}
