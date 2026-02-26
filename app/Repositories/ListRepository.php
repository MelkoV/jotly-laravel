<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ListRepositoryContract;
use App\Data\List\CreateRequestData;
use App\Data\List\ListData;
use App\Data\List\ListFilterData;
use App\Data\List\UpdateRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Enums\ListAccess;
use App\Enums\ListFilterTemplate;
use App\Enums\ListType;
use App\Exceptions\InvalidListTypeException;
use App\Exceptions\ListNotFoundException;
use App\Models\ListItem;
use App\Models\Lists;
use App\Models\ListUser;
use App\Models\ShoppingListItem;
use App\Models\TodoListItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListRepository implements ListRepositoryContract
{
    public function create(CreateRequestData $data): ListData
    {
        /** @var array<string, mixed> $record */
        $record = [
            ...$data->toArray(),
            'access' => ListAccess::Private,
            'touched_at' => Carbon::now(),
            'short_url' => $this->generateUniqueShortUrl(),
        ];
        $model = Lists::create($record);
        return ListData::from($model);
    }

    private function generateUniqueShortUrl(int $length = 10): string
    {
        $shortUrl = Str::random($length);
        if (DB::table('lists')->where('short_url', $shortUrl)->exists()) {
            return $this->generateUniqueShortUrl($length + 1);
        }
        return $shortUrl;
    }

    /**
     * @param UpdateRequestData $data
     * @return ListData
     * @throws ListNotFoundException
     */
    public function update(UpdateRequestData $data): ListData
    {
        $model = Lists::query()->where('id', $data->id)->first();
        if (!$model) {
            throw new ListNotFoundException();
        }
        /** @var array<string, mixed> $record */
        $record = [
            ...$data->toArray(),
            'touched_at' => Carbon::now(),
        ];
        $model->fill($record);
        $model->save();
        return ListData::from($model);
    }

    public function addUser(string $listId, string $userId): void
    {
        ListUser::upsert([
            ['list_id' => $listId, 'user_id' => $userId],
        ], uniqueBy: ['list_id', 'user_id']);
    }

    public function touch(string $id): void
    {
        Lists::where('id', $id)->update(['touched_at' => Carbon::now()]);
    }

    /**
     * @param ListFilterData $filter
     * @return AbstractPaginator<int, ListData>|Enumerable<int, ListData>
     */
    public function getFilteredLists(ListFilterData $filter): AbstractPaginator|Enumerable
    {
        $query = Lists::query()
            ->join('list_users', 'lists.id', '=', 'list_users.list_id')
            ->where('list_users.user_id', $filter->user_id);
        $this->applyFilterToQuery($filter, $query);
        return ListData::collect($query->paginate(
            perPage: $filter->per_page,
            page: $filter->page,
        ));
    }

    /** TODO text search by name and description */
    private function applyFilterToQuery(ListFilterData $filter, Builder $query): void
    {
        if ($filter->is_owner) {
            $query->where('lists.owner_id', $filter->user_id);
        }
        if ($filter->type) {
            $query->where('lists.type', $filter->type);
        }
        if ($filter->template) {
            $query->where('lists.is_template', $filter->template === ListFilterTemplate::Template);
        }
    }

    /**
     * @param string $id
     * @return ListData
     * @throws ListNotFoundException
     */
    public function findById(string $id): ListData
    {
        $model = Lists::query()->where('id', $id)->first();
        if (!$model) {
            throw new ListNotFoundException();
        }
        return ListData::from($model);
    }

    public function createListItem(CreateItemRequestData $data): void
    {
        $listData = $this->findById($data->list_id);
        $listItemModel = $this->makeListItemModel($listData->type);
        /** @var array<string, mixed> $record */
        $record = [
            ...$data->toArray(),
            'data' => $data->toArray(),
        ];
        $listItemModel->fill($record);
    }

    /**
     * @param ListType $type
     * @return ListItem
     * @throws InvalidListTypeException
     */
    private function makeListItemModel(ListType $type): ListItem
    {
        switch ($type) {
            case ListType::Todo:
                return new TodoListItem();
            case ListType::Shopping:
                return new ShoppingListItem();
        }
        throw new InvalidListTypeException();
    }
}
