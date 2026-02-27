<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Data\List\ListData;
use App\Data\List\ListFilterData;
use App\Data\List\CreateRequestData;
use App\Data\List\UpdateRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Data\ListItem\UpdateRequestData as UpdateItemRequestData;
use App\Data\ListItem\ListItemData;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;

interface ListServiceContract
{
    public function create(CreateRequestData $data): ListData;

    public function update(UpdateRequestData $data): ListData;

    /**
     * @param ListFilterData $filter
     * @return AbstractPaginator<int, ListData>|Enumerable<int, ListData>
     */
    public function getFilteredLists(ListFilterData $filter): AbstractPaginator|Enumerable;

    public function createListItem(CreateItemRequestData $data): ListItemData;

    public function updateListItem(UpdateItemRequestData $data): ListItemData;

    /*public function findById(string $id): ListData;

    public function getListItems(string $id);*/
}
