<?php

declare(strict_types=1);

namespace App\Data\List;

use Spatie\LaravelData\Data;

final class UpdateRequestData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }
}
