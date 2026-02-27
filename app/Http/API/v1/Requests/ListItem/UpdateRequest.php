<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\ListItem;

use App\Data\ListItem\CreateRequestData;
use App\Data\ListItem\UpdateRequestData;
use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use App\Rules\CheckCanEditListByItem;
use Illuminate\Validation\Rule;

/**
 * @summary Обновление элемента списка
 *
 * @description
 * Обновление элемента списка
 *
 * @_200 Успешная операция
 *
 * @_422 Ошибка валидации данных
 */
class UpdateRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid', 'bail', new CheckCanEditListByItem()],
            'name' => ['required', 'string', 'min:1', 'max:100'],
            'version' => ['required', 'integer', 'min:1'],
            'priority' => ['nullable', Rule::enum(TodoPriority::class)],
            'description' => ['string', 'nullable', 'max:250'],
            'unit' => ['nullable', Rule::enum(ProductUnit::class)],
            'deadline' => ['nullable', 'date'],
            'price' => ['nullable', 'decimal:0,3'],
            'cost' => ['nullable', 'decimal:0,3'],
            'count' => ['nullable', 'decimal:0,3'],
        ];
    }

    public function toData(): UpdateRequestData
    {
        return UpdateRequestData::from($this->validated());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

}
