<?php

declare(strict_types=1);

namespace App\Data\ListItem;

use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use Spatie\LaravelData\Data;

final class CreateRequestData extends Data
{
    public function __construct(
        public readonly string $user_id,
        public readonly string $list_id,
        public readonly string $name,
        public readonly TodoPriority $priority = TodoPriority::Medium,
        public readonly ?string $description,
        public readonly ?ProductUnit $unit,
        public readonly ?\DateTime $deadline,
        public readonly ?float $price,
        public readonly ?float $cost,
        public readonly ?float $count,
    ) {

    }
}
