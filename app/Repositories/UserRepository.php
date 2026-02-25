<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\UserDevice;
use App\Enums\UserStatus;
use App\Exceptions\UserNotFoundException;
use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserRepository implements UserRepositoryContract
{
    public function create(SignUpData $data): UserData
    {
        /** @var array<string, mixed> $record */
        $record = [
            ...$data->toArray(),
            'status' => UserStatus::Active,
        ];
        $user = User::create($record);
        return UserData::from($user);
    }

    /**
     * @param string $id
     * @return UserData
     * @throws UserNotFoundException
     */
    public function findById(string $id): UserData
    {
        return $this->findRecord($this->getBuilder()->where('id', $id));
    }

    /**
     * @param string $email
     * @return UserData
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): UserData
    {
        return $this->findRecord($this->getBuilder()->where('email', $email));
    }

    /**
     * @return Builder<User>
     */
    private function getBuilder(): Builder
    {
        return User::query()->select(['id', 'name', 'email', 'status', 'avatar']);
    }

    /**
     * @param Builder<User> $builder
     * @return UserData
     * @throws UserNotFoundException
     */
    private function findRecord(Builder $builder): UserData
    {
        $user = $builder->first();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return UserData::from($user);
    }

    public function upsertDevice(UserData $data, UserDevice $device, ?string $deviceId = null): void
    {
        Account::upsert([
            ['user_id' => $data->id, 'device' => $device, 'device_id' => $deviceId, 'last_login_at' => Carbon::now()],
        ], uniqueBy: ['user_id', 'device', 'device_id'], update: ['last_login_at']);
    }
}
