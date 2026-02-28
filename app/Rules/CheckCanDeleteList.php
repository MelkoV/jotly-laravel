<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;

class CheckCanDeleteList extends AccessListRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table('lists')
            ->where('lists.id', $value);
        if (!$this->checkDeleteQuery($query)->exists()) {
            $fail(__('app.list_edit_denied'));
        }
    }
}
