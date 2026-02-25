<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\ListItem\ShoppingData;

/**
 * @property ShoppingData $data
 */
class ShoppingListItem extends ListItem
{
    public function __construct(array $attributes = [])
    {
        $this->casts = array_merge($this->casts, ['data' => ShoppingData::class]);
        parent::__construct($attributes);
    }
}
