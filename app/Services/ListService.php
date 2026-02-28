<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ListRepositoryContract;
use App\Contracts\Services\ListServiceContract;
use App\Data\List\CreateRequestData;
use App\Data\List\ListData;
use App\Data\List\ListFilterData;
use App\Data\List\UpdateRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Data\ListItem\DeleteRequestData as DeleteItemRequestData;
use App\Data\ListItem\UpdateRequestData as UpdateItemRequestData;
use App\Data\ListItem\CompleteRequestData as CompleteItemRequestData;
use App\Data\ListItem\ListItemData;
use App\Exceptions\InvalidListTypeException;
use App\Exceptions\ListNotFoundException;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;

readonly class ListService implements ListServiceContract
{
    public function __construct(
        private ListRepositoryContract $listRepository
    ) {}

    /**
     * @param CreateRequestData $data
     * @return ListData
     */
    public function create(CreateRequestData $data): ListData
    {
        $record = $this->listRepository->create($data);
        $this->listRepository->addUser($record->id, $data->owner_id);
        return $record;
    }

    /**
     * @param UpdateRequestData $data
     * @return ListData
     * @throws ListNotFoundException
     */
    public function update(UpdateRequestData $data): ListData
    {
        return $this->listRepository->update($data);
    }

    /**
     * @param string $id
     * @return ListData
     * @throws ListNotFoundException
     */
    public function findById(string $id): ListData
    {
        return $this->listRepository->findById($id);
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        // @todo delete all users from list by leftUser() method in job
        $this->listRepository->delete($id);
    }

    /**
     * @param string $listId
     * @param string $userId
     * @return void
     */
    public function leftUser(string $listId, string $userId): void
    {
        $this->listRepository->leftUser($listId, $userId);
    }

    /**
     * @param ListFilterData $filter
     * @return AbstractPaginator<int, ListData>|Enumerable<int, ListData>
     */
    public function getFilteredLists(ListFilterData $filter): AbstractPaginator|Enumerable
    {
        return $this->listRepository->getFilteredLists($filter);
    }

    /**
     * @param CreateItemRequestData $data
     * @return ListItemData
     * @throws ListNotFoundException
     * @throws InvalidListTypeException
     */
    public function createListItem(CreateItemRequestData $data): ListItemData
    {
        $this->listRepository->touch($data->list_id);
        return $this->listRepository->createListItem($data);
    }

    /**
     * @param UpdateItemRequestData $data
     * @return ListItemData
     * @throws ListNotFoundException
     */
    public function updateListItem(UpdateItemRequestData $data): ListItemData
    {
        $listItemData = $this->listRepository->updateListItem($data);
        $this->listRepository->touch($listItemData->list_id);
        return $listItemData;
    }

    /**
     * @param CompleteItemRequestData $data
     * @return ListItemData
     * @throws ListNotFoundException
     */
    public function completeListItem(CompleteItemRequestData $data): ListItemData
    {
        $listItemData = $this->updateListItem(UpdateItemRequestData::from($data));
        $this->listRepository->touch($listItemData->list_id);
        return $this->listRepository->completeListItem($listItemData->id, $data->complete_user_id);
    }

    /**
     * @param DeleteItemRequestData $data
     * @return bool
     * @throws ListNotFoundException
     */
    public function deleteListItem(DeleteItemRequestData $data): bool
    {
        return $this->listRepository->deleteListItem($data);
    }

    /**
     * @param string $listId
     * @return AbstractPaginator<int, ListData>|Enumerable<int, ListData>
     */
    public function getListItems(string $listId): AbstractPaginator|Enumerable
    {
        return $this->listRepository->getListItems($listId);
    }
}
