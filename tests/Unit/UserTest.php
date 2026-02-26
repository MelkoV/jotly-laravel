<?php

namespace Tests\Unit;

use _PHPStan_5930232b5\Nette\DI\ServiceCreationException;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\JwtServiceContract;
use App\Data\User\JwtTokenData;
use App\Data\User\SignInData;
use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\JwtTokenType;
use App\Enums\UserDevice;
use App\Enums\UserStatus;
use App\Exceptions\JwtException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_encode_and_decode_jwt_token(): void
    {
        $service = resolve(JwtServiceContract::class);
        $encoded = new JwtTokenData(userId: 'TestUserId', type: JwtTokenType::Refresh);
        $token = $service->encode($encoded);
        $decoded = $service->decode($token);
        $this->assertTrue($decoded->userId === $encoded->userId);
        $this->assertTrue($decoded->type === $encoded->type);
        $this->assertTrue($decoded->type === JwtTokenType::Refresh);
    }

    public function test_decode_expired_jwt_token(): void
    {
        $service = resolve(JwtServiceContract::class);
        $encoded = new JwtTokenData(userId: 'TestUserId', type: JwtTokenType::Refresh, time: 1);
        $token = $service->encode($encoded);
        sleep(2);
        $this->expectException(JwtException::class);
        $this->expectExceptionMessage('Expired token');
        $service->decode($token);
    }

    public function test_user_repository(): void
    {
        $repository = new UserRepository();

        // Create user
        $email = sprintf('%s@test.test', uniqid());
        $user = $repository->create(new SignUpData(
            email: $email,
            password: 'password',
            name: 'Test User',
            device: UserDevice::Web,
            device_id: uniqid()
        ));
        $this->assertEquals($email, $user->email);

        // Get user by id
        $testUser = $repository->findById($user->id);
        $this->assertEquals($email, $testUser->email);

        // Get user by email
        $testUser = $repository->findByEmail($email);
        $this->assertEquals($email, $testUser->email);

        // Get no such user by id
        $this->expectException(UserNotFoundException::class);
        $repository->findById(\Str::uuid());

        // Get no such user by email
        $this->expectException(UserNotFoundException::class);
        $repository->findByEmail(sprintf('%s_fake@test.test', uniqid()));
    }

    public function test_user_service(): void
    {
        $service = new UserService(userRepository: new UserRepositoryDummy());
        $this->app->bind(\Illuminate\Support\Facades\Auth::class, function () {
            return new AuthDummy();
        });

        $user = $service->signUp(new SignUpData(
            email: 'success@test.test',
            password: 'password',
            name: 'Test User',
            device: UserDevice::Web,
            device_id: uniqid()
        ));
        $this->assertEquals('success@test.test', $user->email);

        Auth::shouldReceive('id')->andReturn('success');

        Auth::shouldReceive('attempt')->once()->andReturn(true);
        $user = $service->signIn(new SignInData(
            email: 'success@test.test',
            password: 'password',
            device: UserDevice::Web,
            device_id: uniqid()
        ));
        $this->assertEquals('success@test.test', $user->email);
        $this->assertEquals('success', $user->id);

        Auth::shouldReceive('attempt')->once()->andReturn(false);
        $this->expectException(UserNotFoundException::class);
        $service->signIn(new SignInData(
            email: 'error@test.test',
            password: 'password',
            device: UserDevice::Web,
            device_id: uniqid()
        ));

        $user = $service->profile('success');
        $this->assertEquals('success@test.test', $user->email);
        $this->assertEquals('success', $user->id);

        $this->expectException(UserNotFoundException::class);
        $service->profile('error');
    }
}

class UserRepositoryDummy implements UserRepositoryContract
{
    private UserData $user;
    public function create(SignUpData $data): UserData
    {
        $this->user = new UserData(
            email: $data->email,
            name: $data->name,
            status: UserStatus::Active,
            id: 'success'
        );
        return $this->user;
    }

    public function findById(string $id): UserData
    {
        if ($id === 'error') {
            throw new UserNotFoundException();
        }
        return $this->user;
    }

    public function findByEmail(string $email): UserData
    {
        if ($email === 'error@test.test') {
            throw new UserNotFoundException();
        }
        return $this->user;
    }

    public function upsertDevice(UserData $data, UserDevice $device, ?string $deviceId = null): void
    {
        return;
    }
}
