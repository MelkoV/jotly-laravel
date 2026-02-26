<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\User;

use App\Data\User\SignUpData;
use App\Enums\UserDevice;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rule;

/**
 * @summary Регистрация пользователя
 *
 * @description
 * Регистрация пользователя
 *
 * @_200 Успешная регистрация
 *
 * @_422 Ошибка валидации данных
 */
class SignUpRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:50', 'confirmed:repeat_password'],
            'repeat_password' => ['required', 'string', 'min:8', 'max:50'],
            'name' => ['required'],
            'device' => [Rule::enum(UserDevice::class), 'required'],
            'device_id' => ['required', 'string', 'max:100'],
        ];
    }

    public function toData(): SignUpData
    {
        return SignUpData::from($this->validated());
    }
}
