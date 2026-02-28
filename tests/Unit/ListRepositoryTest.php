<?php

namespace Tests\Unit;

use App\Data\List\CreateRequestData;
use App\Data\List\ListFilterData;
use App\Data\List\UpdateRequestData;
use App\Enums\ListType;
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

        
    }
}
