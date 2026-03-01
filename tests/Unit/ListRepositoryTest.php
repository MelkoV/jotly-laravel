<?php

namespace Tests\Unit;

use App\Data\List\CreateRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Data\ListItem\UpdateRequestData as UpdateItemRequestData;
use App\Data\ListItem\DeleteRequestData as DeleteItemRequestData;
use App\Data\List\ListFilterData;
use App\Data\List\UpdateRequestData;
use App\Enums\ListType;
use App\Exceptions\ListItemNotFoundException;
use App\Exceptions\ListNotFoundException;
use App\Repositories\ListRepository;
use Tests\TestCase;

class ListRepositoryTest extends TestCase
{
    public function test_list_repository_lists_methods(): void
    {
        $repository = new ListRepository();
        $user = $this->getUserData();

        $listData = $repository->create(new CreateRequestData(
            name: 'test',
            owner_id: $user->id,
            is_template: false,
            type: ListType::Shopping,
            description: 'test description',
        ));
        $this->assertTrue($listData->owner_id === $user->id);
        $this->assertTrue($listData->name === 'test');
        $this->assertTrue($listData->description === 'test description');
        $this->assertTrue($listData->type === ListType::Shopping);
        $this->assertTrue($listData->is_template === false);
        $repository->addUser($listData->id, $user->id);

        $testListData = $repository->update(new UpdateRequestData(id: $listData->id, name: 'test edited', description: 'test description edited'));
        $this->assertTrue($testListData->name === 'test edited');
        $this->assertTrue($testListData->id === $listData->id);

        $testListData = $repository->findById($listData->id);
        $this->assertTrue($testListData->id === $listData->id);
        $this->assertTrue($testListData->name === 'test edited');

        $collection = $repository->getFilteredLists(new ListFilterData(user_id: $user->id));
        $this->assertTrue($collection->count() === 1);
        $testListData = $collection->first();
        $this->assertTrue($testListData->id === $listData->id);

        $repository->delete($listData->id);
        $this->expectException(ListNotFoundException::class);
        $repository->findById($listData->id);
    }

    public function test_list_repository_list_items_methods(): void
    {
        $repository = new ListRepository();
        $user = $this->getUserData();
        $listData = $repository->create(new CreateRequestData(
            name: 'test',
            owner_id: $user->id,
            is_template: false,
            type: ListType::Shopping,
            description: 'test description',
        ));

        $listItemData = $repository->createListItem(new CreateItemRequestData(
            user_id: $user->id,
            list_id: $listData->id,
            name: 'test item',
        ));
        $this->assertTrue($listItemData->name === 'test item');
        $this->assertTrue($listItemData->user_name === $user->name);
        $this->assertTrue($listItemData->version === 1);
        $this->assertTrue($listItemData->list_id === $listData->id);

        $testListItemData = $repository->updateListItem(new UpdateItemRequestData(id: $listItemData->id, name: 'test item edited', version: 1));
        $this->assertTrue($testListItemData->name === 'test item edited');
        $this->assertTrue($testListItemData->id === $listItemData->id);
        $this->assertTrue($testListItemData->is_completed === false);
        $this->assertTrue($testListItemData->completed_user_name === null);

        $testListItemData = $repository->completeListItem($listItemData->id, $user->id);
        $this->assertTrue($testListItemData->id === $listItemData->id);
        $this->assertTrue($testListItemData->is_completed === true);
        $this->assertTrue($testListItemData->completed_user_name === $user->name);

        $collection = $repository->getListItems($listData->id);
        $this->assertTrue($collection->count() === 1);
        $testListItemData = $collection->first();
        $this->assertTrue($testListItemData->id === $listItemData->id);

        $repository->deleteListItem(new DeleteItemRequestData(id: $listItemData->id, version: 2));
        $collection = $repository->getListItems($listData->id);
        $this->assertTrue($collection->count() === 0);
        $this->expectException(ListItemNotFoundException::class);
        $testListItemData = $repository->updateListItem(new UpdateItemRequestData(id: $listItemData->id, name: 'test item edited', version: 2));
    }

    public function test_update_list_item_with_incorrect_versions()
    {
        $repository = new ListRepository();
        $user = $this->getUserData();
        $listData = $repository->create(new CreateRequestData(
            name: 'test',
            owner_id: $user->id,
            is_template: false,
            type: ListType::Shopping,
            description: 'test description',
        ));

        $listItemData = $repository->createListItem(new CreateItemRequestData(
            user_id: $user->id,
            list_id: $listData->id,
            name: 'test item',
        ));
        $testListItemData = $repository->updateListItem(new UpdateItemRequestData(id: $listItemData->id, name: 'test item edited', version: 1));
        $this->expectException(ListItemNotFoundException::class);
        $testListItemData = $repository->updateListItem(new UpdateItemRequestData(id: $listItemData->id, name: 'test item edited', version: 1));
    }
}
