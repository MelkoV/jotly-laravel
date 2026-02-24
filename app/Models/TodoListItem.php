<?php

declare(strict_types=1);

namespace App\Models;

use App\DTO\ListItem\TodoData;

/**
 * @property TodoData $data
 */
class TodoListItem extends ListItem
{
    public function __construct(array $attributes = [])
    {
        $this->casts = array_merge($this->casts, ['data' => TodoData::class]);
        parent::__construct($attributes);
    }
}
