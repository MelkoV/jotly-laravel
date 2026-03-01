<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\List;

use App\Data\List\RequestIdAndUserData;
use App\Rules\CheckCanViewList;
use Illuminate\Support\Facades\Auth;

/**
 * @summary Возможные варианты удаления списка
 *
 * @description
 * Возможные варианты удаления списка для пользователя
 *
 * @_200 Успешная операция
 *
 * @_422 Ошибка валидации данных
 */
class DeleteTypesRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid', 'bail', new CheckCanViewList()],
            'user_id' => ['required', 'uuid'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
            'user_id' => Auth::id(),
        ]);
    }

    public function toData(): RequestIdAndUserData
    {
        return RequestIdAndUserData::from($this->validated());
    }
}
