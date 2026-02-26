<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ListRepositoryContract;
use App\Contracts\Services\ListServiceContract;
use App\Data\List\CreateRequestData;
use App\Data\List\ListData;
use App\Data\List\ListFilterData;
use App\Data\List\UpdateRequestData;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;

readonly class ListService implements ListServiceContract
{
    public function __construct(
        private ListRepositoryContract $listRepository
    ) {}

    public function create(CreateRequestData $data): ListData
    {
        $record = $this->listRepository->create($data);
        $this->listRepository->addUser($record->id, $data->owner_id);
        return $record;
    }

    public function update(UpdateRequestData $data): ListData
    {
        return $this->listRepository->update($data);
    }

    /**
     * @param ListFilterData $filter
     * @return AbstractPaginator<int, ListData>|Enumerable<int, ListData>
     */
    public function getFilteredLists(ListFilterData $filter): AbstractPaginator|Enumerable
    {
        return $this->listRepository->getFilteredLists($filter);
    }
}
