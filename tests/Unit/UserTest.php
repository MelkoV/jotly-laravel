<?php

namespace Tests\Unit;

use App\Data\User\JwtTokenData;
use App\Enums\JwtTokenType;
use App\Exceptions\JwtException;
use App\Services\JwtService;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_encode_and_decode_jwt_token(): void
    {
        $service = new JwtService();
        $encoded = JwtTokenData::from(['userId' => 'TestUserId', 'type' => JwtTokenType::Permanent]);
        $token = $service->encode($encoded);
        $decoded = $service->decode($token);
        $this->assertTrue($decoded->userId === $encoded->userId);
        $this->assertTrue($decoded->type === $encoded->type);
        $this->assertTrue($decoded->type === JwtTokenType::Permanent);
    }

    public function test_decode_expired_jwt_token(): void
    {
        $service = new JwtService();
        $encoded = JwtTokenData::from(['userId' => 'TestUserId', 'type' => JwtTokenType::Permanent, 'time' => 1]);
        $token = $service->encode($encoded);
        sleep(2);
        $this->expectException(JwtException::class);
        $this->expectExceptionMessage('Expired token');
        $service->decode($token);
    }
}
