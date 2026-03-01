<?php

namespace Tests;

use App\Contracts\Repositories\ListRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\JwtServiceContract;
use App\Data\List\CreateRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Data\List\ListData;
use App\Data\User\JwtTokenData;
use App\Data\User\SignUpData;
use App\Data\User\UserData;
use App\Enums\JwtTokenType;
use App\Enums\ListType;
use App\Enums\UserDevice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use RonasIT\AutoDoc\Traits\AutoDocTestCaseTrait;

abstract class TestCase extends BaseTestCase
{
    use AutoDocTestCaseTrait;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Регистрируем макрос для AssertableJson
        AssertableJson::macro('hasPagination', function () {
            return $this
                ->hasAll([
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                    'links'
                ]);
        });
    }

    protected function withJwtToken(?string $token = null): TestCase
    {
        if (!$token) {
            $token = $this->getJwtToken();
        }
        return $this->withHeader('Authorization', sprintf('Bearer %s', $token));
    }

    protected function withUserJwtToken(UserData $userData, JwtTokenType $tokenType = JwtTokenType::Temporary): TestCase
    {
        return $this->withJwtToken($this->getJwtToken($userData, $tokenType));
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

    protected function getListData(string $ownerId, string $name = 'Test', bool $isTemplate = false, ListType $type = ListType::Shopping, ?string $description = null): ListData
    {
        $requestData = new CreateRequestData(
            name: $name,
            owner_id: $ownerId,
            is_template: $isTemplate,
            type: $type,
            description: $description,
        );
        $repository = resolve(ListRepositoryContract::class);
        $listData = $repository->create($requestData);
        $repository->addUser($listData->id, $ownerId);
        return $listData;
    }

    protected function getListItemData(?UserData $userData = null, ?ListData $listData = null, string $name = 'Test')
    {
        if (!$userData) {
            $userData = $this->getUserData();
        }
        if (!$listData) {
            $listData = $this->getListData($userData->id);
        }
        $repository = resolve(ListRepositoryContract::class);
        return $repository->createListItem(new CreateItemRequestData(
            user_id: $userData->id,
            list_id: $listData->id,
            name: $name,
        ));
    }
}
