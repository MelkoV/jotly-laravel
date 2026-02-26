<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Data\User\SignInData;
use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\UserDevice;
use App\Exceptions\UserNotFoundException;

interface UserServiceContract
{
    /**
     * @param SignUpData $data
     * @return UserData
     */
    public function signUp(SignUpData $data): UserData;

    /**
     * @param SignInData $data
     * @return UserData
     * @throws UserNotFoundException
     */
    public function signIn(SignInData $data): UserData;

    /**
     * @param UserData $user
     * @param UserDevice $device
     * @param string $deviceId
     * @return void
     */
    public function attachDevice(UserData $user, UserDevice $device, string $deviceId): void;

    /**
     * @param string $userId
     * @return UserData
     * @throws UserNotFoundException
     */
    public function profile(string $userId): UserData;
}
