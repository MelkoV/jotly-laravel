<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\List;

use App\Data\List\UpdateRequestData;
use App\Rules\CheckCanEditList;

/**
 * @summary Редактирование списка
 *
 * @description
 * Редактирование списка
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
            'id' => ['required', 'uuid', 'bail', new CheckCanEditList()],
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['string', 'nullable', 'max:250'],
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
