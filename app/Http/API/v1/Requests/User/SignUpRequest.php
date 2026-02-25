<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\User;

use App\Data\User\SignUpData;
use App\Enums\UserDevice;
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
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'max:50', 'confirmed:repeat_password'],
            'repeat_password' => ['required', 'min:8', 'max:50'],
            'name' => ['required'],
            'device' => [Rule::enum(UserDevice::class), 'required'],
        ];
    }

    public function toData(): SignUpData
    {
        return SignUpData::from($this->validated());
    }
}
