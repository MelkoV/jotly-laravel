<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;

class CheckCanEditListByItem extends AccessEditListRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table('list_items')
            ->where('list_items.id', $value)
            ->join('lists', 'list_items.list_id', '=', 'lists.id');
        if (!$this->checkAccess($query)) {
            $fail(__('app.list_edit_denied'));
        }
    }
}
