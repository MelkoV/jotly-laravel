<?php

declare(strict_types=1);

namespace App\Data\ListItem;

use App\Enums\TodoPriority;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class TodoData extends Data
{
    public function __construct(
        public readonly TodoPriority $priority = TodoPriority::Medium,
        public readonly ?\DateTime $deadline = null,
    ) {
    }
}
