<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\List;

use App\Data\List\CreateRequestData;
use App\Enums\ListType;
use Illuminate\Validation\Rule;

/**
 * @summary Создание нового списка
 *
 * @description
 * Создание нового списка
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
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'type' => [Rule::enum(ListType::class), 'required'],
            'is_template' => ['required', 'boolean'],
            'description' => ['string', 'nullable', 'max:250'],
            'owner_id' => ['required', 'uuid']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'owner_id' => $this->user()->id,
        ]);
    }

    public function toData(): CreateRequestData
    {
        return CreateRequestData::from($this->validated());
    }

}
