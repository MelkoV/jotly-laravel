<?php

namespace Tests\Feature;

use App\Enums\DeleteListType;
use App\Enums\ListType;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListsApiTest extends TestCase
{
    public function test_create_list_token_error()
    {
        $response = $this->postJson('/api/v1/lists');
        $response->assertUnauthorized();
    }

    public function test_create_list_empty_data_error()
    {
        $response = $this->withJwtToken()->postJson('/api/v1/lists', []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['name.0', 'type.0', 'is_template.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_create_list_type_error()
    {
        $response = $this->withJwtToken()->postJson('/api/v1/lists', [
            'name' => 'test',
            'is_template' => false,
            'type' => 'some_type'
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['type.0'])
                ->missing('name')
                ->missing('is_template')
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_create_list_success()
    {
        $userData = $this->getUserData();
        $response = $this->withUserJwtToken($userData)->postJson('/api/v1/lists', [
            'name' => 'test',
            'is_template' => false,
            'type' => ListType::Shopping->value
        ]);
        $response->assertCreated();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll(['id', 'touched_at', 'owner_avatar', 'description'])
            ->where('owner_id', $userData->id)
            ->where('owner_name', $userData->name)
            ->where('is_template', false)
            ->where('name', 'test')
            ->where('can_edit', true)
            ->where('type', ListType::Shopping->value)
            ->missing('errors')
        );
    }

    public function test_update_list_token_error()
    {
        $response = $this->putJson('/api/v1/lists/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_update_list_not_found_error()
    {
        $response = $this->withJwtToken()->putJson('/api/v1/lists/fake_uuid');
        $response->assertUnprocessable();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0', 'name.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_update_list_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        // а пробуем обновить от вновь автоматически созданного
        $response = $this->withJwtToken()->putJson('/api/v1/lists/' . $listData->id, [
            'name' => 'test',
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_update_list_empty_data_error()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        $response = $this->withUserJwtToken($userData)->putJson('/api/v1/lists/' . $listData->id, []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['name.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_update_list_success()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        $response = $this->withUserJwtToken($userData)->putJson('/api/v1/lists/' . $listData->id, [
            'name' => 'test updated',
        ]);
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll(['touched_at', 'owner_avatar', 'description'])
            ->where('id', $listData->id)
            ->where('owner_id', $userData->id)
            ->where('owner_name', $userData->name)
            ->where('is_template', false)
            ->where('name', 'test updated')
            ->where('can_edit', true)
            ->where('type', ListType::Shopping->value)
            ->missing('errors')
        );
    }

    public function test_get_lists_token_error()
    {
        $response = $this->getJson('/api/v1/lists');
        $response->assertUnauthorized();
    }

    public function test_get_lists_success()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id, 'test');
        $response = $this->withUserJwtToken($userData)->getJson('/api/v1/lists');
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('data.0', fn (AssertableJson $json) => $json
                ->hasAll(['touched_at', 'owner_avatar', 'description'])
                ->where('id', $listData->id)
                ->where('owner_id', $userData->id)
                ->where('owner_name', $userData->name)
                ->where('is_template', false)
                ->where('name', 'test')
                ->where('can_edit', true)
                ->where('type', ListType::Shopping->value)
            )
            ->hasPagination()
            ->missing('errors')
        );
    }

    public function test_view_list_token_error()
    {
        $response = $this->getJson('/api/v1/lists/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_view_list_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        // а пробуем получить от вновь автоматически созданного
        $response = $this->withJwtToken()->getJson('/api/v1/lists/' . $listData->id);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_view_list_success()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id, 'test');
        $response = $this->withUserJwtToken($userData)->getJson('/api/v1/lists/' . $listData->id);
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('model', fn (AssertableJson $json) => $json
                ->hasAll(['touched_at', 'owner_avatar', 'description'])
                ->where('id', $listData->id)
                ->where('owner_id', $userData->id)
                ->where('owner_name', $userData->name)
                ->where('is_template', false)
                ->where('name', 'test')
                ->where('can_edit', true)
                ->where('type', ListType::Shopping->value)
            )
            ->has('items')
            ->missing('errors')
        );
    }

    public function test_get_list_delete_types_token_error()
    {
        $response = $this->getJson('/api/v1/lists/delete-types/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_get_list_delete_types_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        // а пробуем получить от вновь автоматически созданного
        $response = $this->withJwtToken()->getJson('/api/v1/lists/delete-types/' . $listData->id);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_get_list_delete_types_success()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id, 'test');
        $response = $this->withUserJwtToken($userData)->getJson('/api/v1/lists/delete-types/' . $listData->id);
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where(DeleteListType::Delete->value, true)
            ->where(DeleteListType::Left->value, true)
            ->missing('errors')
        );
    }

    public function test_left_list_token_error()
    {
        $response = $this->deleteJson('/api/v1/lists/left/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_left_list_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        // а пробуем получить от вновь автоматически созданного
        $response = $this->withJwtToken()->deleteJson('/api/v1/lists/left/' . $listData->id);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0'])
            )
            ->has('message')
        );
    }

    public function test_left_list_success()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        $response = $this->withUserJwtToken($userData)->deleteJson('/api/v1/lists/left/' . $listData->id);
        $response->assertOk();

        $response = $this->withJwtToken()->getJson('/api/v1/lists/' . $listData->id);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_delete_list_token_error()
    {
        $response = $this->deleteJson('/api/v1/lists/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_delete_list_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        // а пробуем получить от вновь автоматически созданного
        $response = $this->withJwtToken()->deleteJson('/api/v1/lists/' . $listData->id);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0'])
            )
            ->has('message')
        );
    }

    public function test_delete_list_success()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        $response = $this->withUserJwtToken($userData)->deleteJson('/api/v1/lists/' . $listData->id);
        $response->assertOk();

        $response = $this->withJwtToken()->getJson('/api/v1/lists/' . $listData->id);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_create_list_item_token_error()
    {
        $response = $this->postJson('/api/v1/list-items');
        $response->assertUnauthorized();
    }

    public function test_create_list_item_empty_data_error()
    {
        $response = $this->withJwtToken()->postJson('/api/v1/list-items', []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['name.0', 'list_id.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_create_list_item_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        // а отправляем запрос от вновь автоматически созданного
        $response = $this->withJwtToken()->postJson('/api/v1/list-items', [
            'name' => 'test',
            'list_id' => $listData->id,
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['list_id.0'])
                ->missing('name')
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_create_list_item_success()
    {
        $userData = $this->getUserData();
        $listData = $this->getListData($userData->id);
        $response = $this->withUserJwtToken($userData)->postJson('/api/v1/list-items', [
            'name' => 'test',
            'list_id' => $listData->id,
        ]);
        $response->assertCreated();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll(['id', 'attributes', 'user_avatar', 'description', 'completed_user_avatar'])
            ->where('list_id', $listData->id)
            ->where('user_name', $userData->name)
            ->where('version', 1)
            ->where('name', 'test')
            ->where('is_completed', false)
            ->where('completed_user_name', null)
            ->missing('errors')
        );

        $response = $this->withUserJwtToken($userData)->getJson('/api/v1/lists/' . $listData->id);
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('model')
            ->has('items.0', fn (AssertableJson $json) => $json
                ->hasAll(['id', 'attributes', 'user_avatar', 'description', 'completed_user_avatar'])
                ->where('list_id', $listData->id)
                ->where('user_name', $userData->name)
                ->where('version', 1)
                ->where('name', 'test')
                ->where('is_completed', false)
                ->where('completed_user_name', null)
            )
            ->missing('errors')
        );
    }

    public function test_update_list_item_token_error()
    {
        $response = $this->putJson('/api/v1/list-items/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_update_list_item_empty_data_error()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withJwtToken()->putJson('/api/v1/list-items/' . $listItemData->id, []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['name.0', 'id.0', 'version.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_update_list_item_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        // а отправляем запрос от вновь автоматически созданного
        $response = $this->withJwtToken()->putJson('/api/v1/list-items/' . $listItemData->id, [
            'name' => 'test updated',
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0', 'version.0'])
                ->missing('name')
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_update_list_item_version_error()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withUserJwtToken($userData)->putJson('/api/v1/list-items/' . $listItemData->id, [
            'name' => 'test updated',
            'version' => 2,
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('message')
            ->missing('id')
        );
    }

    public function test_update_list_item_success()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withUserJwtToken($userData)->putJson('/api/v1/list-items/' . $listItemData->id, [
            'name' => 'test updated',
            'version' => 1,
        ]);
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll(['id', 'attributes', 'user_avatar', 'description', 'completed_user_avatar'])
            ->where('list_id', $listItemData->list_id)
            ->where('id', $listItemData->id)
            ->where('user_name', $userData->name)
            ->where('version', 2)
            ->where('name', 'test updated')
            ->where('is_completed', false)
            ->where('completed_user_name', null)
            ->missing('errors')
        );
    }

    public function test_complete_list_item_token_error()
    {
        $response = $this->putJson('/api/v1/list-items/complete/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_complete_list_item_empty_data_error()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withJwtToken()->putJson('/api/v1/list-items/complete/' . $listItemData->id, []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['name.0', 'id.0', 'version.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_complete_list_item_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        // а отправляем запрос от вновь автоматически созданного
        $response = $this->withJwtToken()->putJson('/api/v1/list-items/complete/' . $listItemData->id, [
            'name' => 'test completed',
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0', 'version.0'])
                ->missing('name')
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_complete_list_item_version_error()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withUserJwtToken($userData)->putJson('/api/v1/list-items/complete/' . $listItemData->id, [
            'name' => 'test completed',
            'version' => 2,
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('message')
            ->missing('id')
        );
    }

    public function test_complete_list_item_success()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withUserJwtToken($userData)->putJson('/api/v1/list-items/complete/' . $listItemData->id, [
            'name' => 'test completed',
            'version' => 1,
        ]);
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll(['id', 'attributes', 'user_avatar', 'description', 'completed_user_avatar'])
            ->where('list_id', $listItemData->list_id)
            ->where('id', $listItemData->id)
            ->where('user_name', $userData->name)
            ->where('version', 2)
            ->where('name', 'test completed')
            ->where('is_completed', true)
            ->where('completed_user_name', $userData->name)
            ->missing('errors')
        );
    }

    public function test_delete_list_item_token_error()
    {
        $response = $this->deleteJson('/api/v1/list-items/fake_uuid');
        $response->assertUnauthorized();
    }

    public function test_delete_list_item_empty_data_error()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withJwtToken()->deleteJson('/api/v1/list-items/' . $listItemData->id, []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0', 'version.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_delete_list_item_access_error()
    {
        // создаем список от конкретного пользователя
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        // а отправляем запрос от вновь автоматически созданного
        $response = $this->withJwtToken()->deleteJson('/api/v1/list-items/' . $listItemData->id, []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['id.0', 'version.0'])
            )
            ->has('message')
            ->missing('id')
        );
    }

    public function test_delete_list_item_version_error()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withUserJwtToken($userData)->deleteJson('/api/v1/list-items/' . $listItemData->id, [
            'version' => 2,
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('message')
            ->missing('id')
        );
    }

    public function test_delete_list_item_success()
    {
        $userData = $this->getUserData();
        $listItemData = $this->getListItemData($userData);
        $response = $this->withUserJwtToken($userData)->deleteJson('/api/v1/list-items/' . $listItemData->id, [
            'version' => 1,
        ]);
        $response->assertOk();

        $response = $this->withUserJwtToken($userData)->getJson('/api/v1/lists/' . $listItemData->list_id);
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('model')
            ->has('items')
            ->missing('items.0')
            ->missing('errors')
        );
    }
}
