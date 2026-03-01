<?php

declare(strict_types=1);

namespace App\Data\ListItem;

use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class ListItemAttributesData extends Data
{
    public function __construct(
        public readonly ?TodoPriority $priority,
        public readonly ?ProductUnit $unit,
        public readonly ?Carbon $deadline,
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
        $json = json_encode($result, $options);
        if ($json === false) {
            throw new \Exception(sprintf('Can not convert ListItemAttributesData to json: %s', json_last_error_msg()));
        }
        return $json;
    }
}
