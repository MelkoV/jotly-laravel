<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Data\List\ListData;
use App\Data\List\ListFilterData;
use App\Data\List\CreateRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Data\List\UpdateRequestData;
use App\Exceptions\ListNotFoundException;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;

interface ListRepositoryContract
{
    public function create(CreateRequestData $data): ListData;

    public function update(UpdateRequestData $data): ListData;

    public function addUser(string $listId, string $userId): void;

    public function touch(string $id): void;

    /**
     * @param ListFilterData $filter
     * @return AbstractPaginator<int, ListData>|Enumerable<int, ListData>
     */
    public function getFilteredLists(ListFilterData $filter): AbstractPaginator|Enumerable;

    public function findById(string $id): ListData;

    public function createListItem(CreateItemRequestData $data): void;

    /*public function delete(string $id): bool;

    public function getShoppingItems(string $id);

    public function getTodoItems(string $id);*/
}
