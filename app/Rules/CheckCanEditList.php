<?php

namespace App\Rules;

use App\Enums\ListAccess;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CheckCanEditList implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('lists')
            ->join('list_users', 'list_users.list_id', '=', 'lists.id')
            ->where('lists.id', $value)
            ->where('list_users.user_id', auth()->id())
            ->whereNull('lists.deleted_at')
            ->where(function ($query) {
                /** @var Builder $query */
                $query->where('lists.owner_id', auth()->id())
                    ->orWhereRaw('(lists.access & ?) > 0', [ListAccess::CanEdit->value]);
            })
            ->exists();
        if (!$exists) {
            $fail(__('app.cant_edit_list'));
        }
    }
}
