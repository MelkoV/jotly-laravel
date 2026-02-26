<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\JwtServiceContract;
use App\Data\User\JwtTokenData;
use App\Enums\JwtTokenType;
use App\Exceptions\JwtException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService implements JwtServiceContract
{
    private string $alg;
    private string $key;

    public function __construct()
    {
        $this->alg = \Config::string('services.jwt.alg');
        $this->key = \Config::string('services.jwt.key');
    }

    public function encode(JwtTokenData $data): string
    {
        return JWT::encode($data->toArray(), $this->key, $this->alg);
    }

    /**
     * @param string $token
     * @return JwtTokenData
     * @throws JwtException
     */
    public function decode(string $token): JwtTokenData
    {
        try {
            return JwtTokenData::from(JWT::decode($token, new Key($this->key, $this->alg)));
        } catch (\Exception $e) {
            throw new JwtException($e->getMessage());
        }
    }
}
