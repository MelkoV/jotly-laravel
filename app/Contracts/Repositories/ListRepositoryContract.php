<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Data\List\ListData;
use App\Data\List\ListFilterData;
use App\Data\List\CreateRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Data\ListItem\DeleteRequestData as DeleteItemRequestData;
use App\Data\ListItem\UpdateRequestData as UpdateItemRequestData;
use App\Data\List\UpdateRequestData;
use App\Data\ListItem\ListItemData;
use App\Exceptions\InvalidListTypeException;
use App\Exceptions\ListNotFoundException;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;

interface ListRepositoryContract
{
    /**
     * @param CreateRequestData $data
     * @return ListData
     */
    public function create(CreateRequestData $data): ListData;

    /**
     * @param UpdateRequestData $data
     * @return ListData
     * @throws ListNotFoundException
     */
    public function update(UpdateRequestData $data): ListData;

    /**
     * @param string $id
     * @return ListData
     * @throws ListNotFoundException
     */
    public function findById(string $id): ListData;

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void;

    /**
     * @param string $listId
     * @param string $userId
     * @return void
     */
    public function leftUser(string $listId, string $userId): void;

    /**
     * @param string $listId
     * @param string $userId
     * @return void
     */
    public function addUser(string $listId, string $userId): void;

    /**
     * @param string $id
     * @return void
     */
    public function touch(string $id): void;

    /**
     * @param ListFilterData $filter
     * @return AbstractPaginator<int, ListData>|Enumerable<int, ListData>
     */
    public function getFilteredLists(ListFilterData $filter): AbstractPaginator|Enumerable;

    /**
     * @param CreateItemRequestData $data
     * @return ListItemData
     * @throws ListNotFoundException
     * @throws InvalidListTypeException
     */
    public function createListItem(CreateItemRequestData $data): ListItemData;

    /**
     * @param UpdateItemRequestData $data
     * @return ListItemData
     * @throws ListNotFoundException
     */
    public function updateListItem(UpdateItemRequestData $data): ListItemData;

    /**
     * @param string $listItemId
     * @param string $completeUserId
     * @return ListItemData
     * @throws ListNotFoundException
     */
    public function completeListItem(string $listItemId, string $completeUserId): ListItemData;

    /**
     * @param DeleteItemRequestData $data
     * @return bool
     * @throws ListNotFoundException
     */
    public function deleteListItem(DeleteItemRequestData $data): bool;

    /**
     * @param string $listId
     * @return AbstractPaginator<int, ListItemData>|Enumerable<int, ListItemData>
     */
    public function getListItems(string $listId): AbstractPaginator|Enumerable;
}
