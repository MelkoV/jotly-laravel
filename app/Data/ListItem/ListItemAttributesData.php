<?php

declare(strict_types=1);

namespace App\Data\ListItem;

use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use Spatie\LaravelData\Data;

class ListItemAttributesData extends Data
{
    public function __construct(
        public readonly ?TodoPriority $priority,
        public readonly ?ProductUnit $unit,
        public readonly ?\DateTime $deadline,
        public readonly ?float $price,
        public readonly ?float $cost,
        public readonly ?float $count,
    ) {
    }

    public function toJson($options = 0): string
    {
        $data = $this->transform();
        $result = collect($data)->filter(function ($value) {
            return $value !== null;
        })->all();
        return json_encode($result, $options);
    }

}
