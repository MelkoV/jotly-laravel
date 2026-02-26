<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Data\User\JwtTokenData;
use App\Enums\JwtTokenType;
use App\Exceptions\JwtException;

interface JwtServiceContract
{
    /**
     * @param JwtTokenData $data
     * @return string
     */
    public function encode(JwtTokenData $data): string;

    /**
     * @param string $token
     * @return JwtTokenData
     * @throws JwtException
     */
    public function decode(string $token): JwtTokenData;
}
