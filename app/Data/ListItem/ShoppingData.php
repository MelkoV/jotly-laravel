<?php

declare(strict_types=1);

namespace App\Data\ListItem;

use App\Enums\ProductUnit;
use Spatie\LaravelData\Data;

class ShoppingData extends Data
{
    public function __construct(
        public ?float $price,
        public ?float $cost,
        public ?float $count,
        public ?ProductUnit $unit,
    ) {
    }
}
