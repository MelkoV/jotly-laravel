<?php

declare(strict_types=1);

namespace App\Data\List;

use App\Data\ListItem\ListItemData;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Enumerable;
use Spatie\LaravelData\Data;

final class ListViewData extends Data
{
    public function __construct(
       public readonly ListData $model,
       /** @var AbstractPaginator<int, ListItemData>|Enumerable<int, ListItemData> */
       public readonly AbstractPaginator|Enumerable $items,
    ) {
    }
}
