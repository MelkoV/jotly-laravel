<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\UserDevice;
use App\Exceptions\UserNotFoundException;

interface UserRepositoryContract
{
    /**
     * @param SignUpData $data
     * @return UserData
     */
    public function create(SignUpData $data): UserData;

    /**
     * @param string $id
     * @return UserData
     * @throws UserNotFoundException
     */
    public function findById(string $id): UserData;

    /**
     * @param string $email
     * @return UserData
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): UserData;

    /**
     * @param UserData $data
     * @param UserDevice $device
     * @param string|null $deviceId
     * @return void
     */
    public function upsertDevice(UserData $data, UserDevice $device, ?string $deviceId = null): void;
}
