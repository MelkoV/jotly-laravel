<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\ListItem;

use App\Data\ListItem\CreateRequestData;
use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use App\Rules\CheckCanEditList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @summary Создание нового элемента списка
 *
 * @description
 * Создание нового элемента списка
 *
 * @swaggerIgnore user_id
 *
 * @_201 Успешная операция
 *
 * @_422 Ошибка валидации данных
 */
class CreateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'uuid'],
            'list_id' => ['required', 'uuid', 'bail', new CheckCanEditList()],
            'name' => ['required', 'string', 'min:1', 'max:100'],
            'priority' => ['nullable', Rule::enum(TodoPriority::class)],
            'description' => ['string', 'nullable', 'max:250'],
            'unit' => ['nullable', Rule::enum(ProductUnit::class)],
            'deadline' => ['nullable', 'date'],
            'price' => ['nullable', 'decimal:0,3'],
            'cost' => ['nullable', 'decimal:0,3'],
            'count' => ['nullable', 'decimal:0,3'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => Auth::id(),
        ]);
    }

    public function toData(): CreateRequestData
    {
        return CreateRequestData::from($this->validated());
    }
}
