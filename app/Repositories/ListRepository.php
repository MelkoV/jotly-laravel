<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ListRepositoryContract;
use App\Data\List\CreateRequestData;
use App\Data\List\ListData;
use App\Data\List\ListFilterData;
use App\Data\List\UpdateRequestData;
use App\Data\ListItem\DeleteRequestData as DeleteItemRequestData;
use App\Data\ListItem\UpdateRequestData as UpdateItemRequestData;
use App\Data\ListItem\CreateRequestData as CreateItemRequestData;
use App\Data\ListItem\ListItemData;
use App\Enums\ListAccess;
use App\Enums\ListFilterTemplate;
use App\Exceptions\InvalidListTypeException;
use App\Exceptions\ListItemNotFoundException;
use App\Exceptions\ListNotFoundException;
use App\Models\ListItem;
use App\Models\Lists;
use App\Models\ListUser;
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
            ->where('list_users.user_id', $filter->user_id)
            ->whereNull('lists.deleted_at')
            ->orderByDesc('lists.touched_at');
        $this->applyFilterToQuery($filter, $query);
        return ListData::collect($query->paginate(
            perPage: $filter->per_page,
            page: $filter->page,
        ));
    }

    /**
     * @param ListFilterData $filter
     * @param Builder<Lists> $query
     * @return void
     * @TODO text search by name and description
     */
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

    public function delete(string $id): void
    {
        ListUser::query()->where('list_id', $id)->delete();
        Lists::query()->where('id', $id)->delete();
    }

    public function leftUser(string $listId, string $userId): void
    {
        ListUser::query()
            ->where('list_id', $listId)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * @param CreateItemRequestData $data
     * @return ListItemData
     * @throws InvalidListTypeException
     * @throws ListNotFoundException
     */
    public function createListItem(CreateItemRequestData $data): ListItemData
    {
        /** @var array<string, mixed> $record */
        $record = [
            ...$data->toArray(),
            'version' => 1,
            'data' => $data->toArray(),
            'is_completed' => false,
        ];
        $listItemModel = ListItem::create($record);
        return ListItemData::from([
            ...$listItemModel->toArray(),
            'attributes' => $listItemModel->data,
        ]);
    }


    /**
     * @param UpdateItemRequestData $data
     * @return ListItemData
     * @throws ListItemNotFoundException
     */
    public function updateListItem(UpdateItemRequestData $data): ListItemData
    {
        $model = $this->findListItem($data->id, $data->version);
        /** @var array<string, mixed> $record */
        $record = [
            ...$data->toArray(),
            'data' => $data->toArray(),
            'version' => $data->version + 1,
        ];
        $model->fill($record);
        $model->save();
        return ListItemData::from($model);
    }

    /**
     * @param string $listItemId
     * @param string $completeUserId
     * @return ListItemData
     * @throws ListItemNotFoundException
     */
    public function completeListItem(string $listItemId, string $completeUserId): ListItemData
    {
        $model = $this->findListItem($listItemId);
        if ($model->is_completed) {
            return ListItemData::from($model);
        }
        $model->fill([
            'is_completed' => true,
            'completed_at' => Carbon::now(),
            'completed_user_id' => $completeUserId
        ]);
        $model->save();
        $model->refresh();
        return ListItemData::from($model);
    }

    /**
     * @param DeleteItemRequestData $data
     * @return bool
     * @throws ListItemNotFoundException
     */
    public function deleteListItem(DeleteItemRequestData $data): bool
    {
        $model = $this->findListItem($data->id, $data->version);
        return $model->delete() === true;
    }

    /**
     * @param string $listItemId
     * @param int|null $version
     * @return ListItem
     * @throws ListItemNotFoundException
     */
    private function findListItem(string $listItemId, ?int $version = null): ListItem
    {
        $query = ListItem::query()->where('id', $listItemId);
        if ($version !== null) {
            $query->where('version', $version);
        }
        $model = $query->first();
        if (!$model) {
            throw new ListItemNotFoundException(__('app.list_item_not_exists'));
        }
        return $model;
    }

    /**
     * @param string $listId
     * @return AbstractPaginator<int, ListItemData>|Enumerable<int, ListItemData>
     */
    public function getListItems(string $listId): AbstractPaginator|Enumerable
    {
        $query = ListItem::query()
            ->where('list_id', $listId)
            ->orderBy('is_completed')
            ->orderBy('created_at');
        return ListItemData::collect($query->get());
    }
}
