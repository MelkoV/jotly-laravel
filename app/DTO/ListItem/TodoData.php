<?php

declare(strict_types=1);

namespace App\DTO\ListItem;

use App\Enums\TodoPriority;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class TodoData extends Data
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'Y-m-d\TH:i:sP'])]
        public ?Carbon $deadline,
        public ?TodoPriority $priority,
    ) {
    }
}
