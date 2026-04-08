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
 * @swaggerIgnore id
 *
 * @_200 Успешное редактирование
 *
 * @_422 Ошибка валидации входных данных
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
            'name' => ['required', 'string', 'min:1', 'max:100'],
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
