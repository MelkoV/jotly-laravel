<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\ListItem;

use App\Data\ListItem\CompleteRequestData;
use App\Data\ListItem\DeleteRequestData;
use App\Rules\CheckCanEditListByItem;

/**
 * @summary Удаление элемента списка
 *
 * @description
 * Удаление элемента списка
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
            'id' => ['required', 'uuid', 'bail', new CheckCanEditListByItem()],
            'version' => ['required', 'integer', 'min:1'],
        ];
    }

    public function toData(): DeleteRequestData
    {
        return DeleteRequestData::from($this->validated());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
