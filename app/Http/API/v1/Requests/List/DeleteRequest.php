<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\List;

use App\Rules\CheckCanDeleteList;

/**
 * @summary Удалить список
 *
 * @description
 * Удалить список для всех пользователей
 *
 * @_200 Успешная операция
 *
 * @_422 Ошибка валидации данных
 */
class DeleteRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid', 'bail', new CheckCanDeleteList()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
