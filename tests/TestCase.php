<?php

namespace Tests;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\JwtServiceContract;
use App\Data\User\JwtTokenData;
use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\JwtTokenType;
use App\Enums\UserDevice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RonasIT\AutoDoc\Traits\AutoDocTestCaseTrait;

abstract class TestCase extends BaseTestCase
{
    use AutoDocTestCaseTrait;
    use DatabaseTransactions;

    protected function withJwtToken(?string $token = null): TestCase
    {
        if (!$token) {
            $token = $this->getJwtToken();
        }
        return $this->withHeader('Authorization', sprintf('Bearer %s', $token));
    }

    protected function getJwtToken(?UserData $userData = null, JwtTokenType $tokenType = JwtTokenType::Temporary): string
    {
        if (!$userData) {
            $userData = $this->getUserData();
        }
        $service = resolve(JwtServiceContract::class);
        return $service->encode(
            new JwtTokenData(
                userId: $userData->id,
                type: $tokenType,
            )
        );
    }

    protected function getUserData(?string $email = null, ?string $password = null, ?string $name = null, UserDevice $device = UserDevice::Web, string $deviceId = 'test_device_id'): UserData
    {
        $signUpData = new SignUpData(
            email: $email ?? sprintf('%s@test.test', uniqid()),
            password: $password ?? 'test1234',
            name: $name ?? 'TestCase User',
            device: $device,
            device_id: $deviceId,
        );
        $repository = resolve(UserRepositoryContract::class);
        return $repository->create($signUpData);
    }
}
