<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\List;

use App\Data\List\CreateRequestData;
use App\Data\List\ListFilterData;
use App\Enums\ListFilterTemplate;
use App\Enums\ListType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @summary Отфильтрованная коллекция списков пользователя
 *
 * @description
 * Отфильтрованная коллекция списков пользователя
 *
 * @_200 Успешная операция
 *
 * @_422 Ошибка валидации данных
 */
class FilteredListRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'text' => ['nullable', 'string', 'max:100'],
            'type' => [Rule::enum(ListType::class), 'nullable'],
            'template' => [Rule::enum(ListFilterTemplate::class), 'nullable'],
            'is_owner' => ['boolean', 'required'],
            'page' => ['integer', 'required', 'min:1'],
            'per_page' => ['integer', 'required', 'min:1', 'max:100'],
            'user_id' => ['required', 'uuid']
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_owner' => $this->is_owner ?? false,
            'page' => $this->page ?? 1,
            'per_page' => $this->per_page ?? 100,
            'user_id' => Auth::id(),
        ]);
    }

    public function toData(): ListFilterData
    {
        return ListFilterData::from($this->validated());
    }
}
