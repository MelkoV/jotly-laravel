<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ListAccess;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;

abstract class AccessEditListRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    abstract public function validate(string $attribute, mixed $value, \Closure $fail): void;

    protected function checkAccess(Builder $query): bool
    {
        return $query
            ->join('list_users', 'list_users.list_id', '=', 'lists.id')
            ->where('list_users.user_id', auth()->id())
            ->whereNull('lists.deleted_at')
            ->where(function ($query) {
                /** @var Builder $query */
                $query->where('lists.owner_id', auth()->id())
                    ->orWhereRaw('(lists.access & ?) > 0', [ListAccess::CanEdit->value]);
            })
            ->exists();
    }
}
