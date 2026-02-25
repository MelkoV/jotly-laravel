<?php

declare(strict_types=1);

namespace App\Http\API\v1\Requests\User;

use App\Data\User\SignInData;
use App\Enums\UserDevice;
use App\Enums\UserStatus;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * @summary Авторизация пользователя
 *
 * @description
 * Авторизация пользователя
 *
 * @_200 Успешная операция
 *
 * @_422 Ошибка валидации данных
 */
class SignInRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @return array<string, list<string|object>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('users', 'email')->where(function ($query) {
                /** @var Exists $query */
                $query->where('status', UserStatus::Active);
            })],
            'password' => ['required', 'min:8', 'max:50'],
            'device' => [Rule::enum(UserDevice::class), 'required'],
        ];
    }

    public function toData(): SignInData
    {
        return SignInData::from($this->validated());
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.exists' => __('auth.failed'),
        ];
    }
}
