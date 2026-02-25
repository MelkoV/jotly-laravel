<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\UserServiceContract;
use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\UserDevice;

class UserService implements UserServiceContract
{
    public function __construct(
        private readonly UserRepositoryContract $userRepository,
    ) {
    }

    public function signUp(SignUpData $data): UserData
    {
        $user = $this->userRepository->create($data);
        $this->attachDevice($user, $data->device);
        // @TODO get user avatar by email
        // @TODO send confirmation email
        return $user;
    }

    public function signIn(): UserData
    {

    }

    public function attachDevice(UserData $user, UserDevice $device, ?string $deviceId = null): void
    {
        $this->userRepository->upsertDevice($user, $device, $deviceId);
    }
}
