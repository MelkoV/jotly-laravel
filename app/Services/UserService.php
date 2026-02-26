<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\UserServiceContract;
use App\Data\User\SignInData;
use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\UserDevice;
use App\Exceptions\UserNotFoundException;
use Illuminate\Support\Facades\Auth;

readonly class UserService implements UserServiceContract
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {
    }

    public function signUp(SignUpData $data): UserData
    {
        $user = $this->userRepository->create($data);
        $this->attachDevice($user, $data->device, $data->device_id);
        // @TODO fetch user avatar by email
        // @TODO send confirmation email
        // @TODO check invited lists
        return $user;
    }

    /**
     * @param SignInData $data
     * @return UserData
     * @throws UserNotFoundException
     */
    public function signIn(SignInData $data): UserData
    {
        if (!Auth::attempt($data->toAttemptArray())) {
            throw new UserNotFoundException();
        }
        $user = $this->userRepository->findById((string)Auth::id());
        $this->attachDevice($user, $data->device, $data->device_id);
        // @TODO fetch user avatar by email
        return $user;
    }

    public function attachDevice(UserData $user, UserDevice $device, string $deviceId): void
    {
        $this->userRepository->upsertDevice($user, $device, $deviceId);
    }

    /**
     * @param string $userId
     * @return UserData
     * @throws UserNotFoundException
     */
    public function profile(string $userId): UserData
    {
        return $this->userRepository->findById($userId);
    }
}
