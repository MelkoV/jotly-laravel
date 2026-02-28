<?php

declare(strict_types=1);

namespace App\Data\ListItem;

use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use Spatie\LaravelData\Data;

final class DeleteRequestData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly int $version,
    ) {

    }
}
