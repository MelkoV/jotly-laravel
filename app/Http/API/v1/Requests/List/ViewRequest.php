<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\List;

use App\Rules\CheckCanViewList;

/**
 * @summary Просмотр списка
 *
 * @description
 * Просмотр списка
 *
 * @_200 Успешная операция
 *
 * @_422 Ошибка валидации данных
 */
class ViewRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid', 'bail', new CheckCanViewList()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
