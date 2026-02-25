<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Data\User\JwtTokenData;

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
     */
    public function decode(string $token): JwtTokenData;
}
