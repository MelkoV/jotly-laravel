<?php

namespace Tests\Feature;

use App\Enums\JwtTokenType;
use App\Enums\UserDevice;
use App\Enums\UserStatus;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    public function test_sign_up_empty_data()
    {
        $response = $this->postJson('/api/v1/user/sign-up', []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['email.0', 'password.0', 'repeat_password.0', 'name.0', 'device.0', 'device_id.0'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_up_incorrect_email()
    {
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => 'test',
            'password' => 'test1234',
            'repeat_password' => 'test1234',
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('email.0')
                ->missingAll(['password', 'repeat_password', 'name', 'device', 'device_id'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_up_incorrect_password()
    {
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => 'test@test.test',
            'password' => 'test',
            'repeat_password' => 'test1234',
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('password.0')
                ->missingAll(['email', 'repeat_password', 'name', 'device', 'device_id'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_up_incorrect_name()
    {
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => 'test@test.test',
            'password' => 'test1234',
            'repeat_password' => 'test1234',
            'name' => '',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('name.0')
                ->missingAll(['email', 'repeat_password', 'password', 'device', 'device_id'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_up_incorrect_device()
    {
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => 'test@test.test',
            'password' => 'test1234',
            'repeat_password' => 'test1234',
            'name' => 'test',
            'device' => 'fake_device',
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('device.0')
                ->missingAll(['email', 'repeat_password', 'password', 'name', 'device_id'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_up_incorrect_repeat_password()
    {
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => 'test@test.test',
            'password' => 'test1234',
            'repeat_password' => 'test9876',
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('password.0')
                ->missingAll(['email', 'repeat_password', 'name', 'device'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_up_correct_data()
    {
        $email = time() . '@test.test';
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => 'test1234',
            'repeat_password' => 'test1234',
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('user', fn (AssertableJson $json) => $json
                ->hasAll(['id', 'email', 'name', 'status', 'avatar'])
                ->where('email', $email)
                ->where('name', 'test')
                ->where('status', UserStatus::Active)
                ->whereType('id', 'string')
            )
            ->whereType('token', 'string')
            ->missing('errors')
        )->assertCookie('refresh_token');
    }

    public function test_sign_up_duplicate_email()
    {
        $email = time() . '@test.test';
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => 'test1234',
            'repeat_password' => 'test1234',
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => 'test1234another',
            'repeat_password' => 'test1234another',
            'name' => 'test another',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('email.0')
                ->missingAll(['password', 'repeat_password', 'name', 'device'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_in_empty_data()
    {
        $response = $this->postJson('/api/v1/user/sign-in', []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['email.0', 'password.0', 'device.0', 'device_id.0'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_in_incorrect_device()
    {
        $email = time() . '@test.test';
        $password = 'test1234';
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => $password,
            'repeat_password' => $password,
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => $password,
            'device' => 'fake_device',
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('device.0')
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_in_correct_data()
    {
        $email = time() . '@test.test';
        $password = 'test1234';
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => $password,
            'repeat_password' => $password,
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => $password,
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('user', fn (AssertableJson $json) => $json
                ->hasAll(['id', 'email', 'name', 'status', 'avatar'])
                ->where('email', $email)
                ->where('name', 'test')
                ->where('status', UserStatus::Active)
                ->whereType('id', 'string')
            )
            ->whereType('token', 'string')
            ->missing('errors')
        )->assertCookie('refresh_token');
    }

    public function test_sign_in_incorrect_credentials()
    {
        $email = time() . '@test.test';
        $password = 'test1234';
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => $password,
            'repeat_password' => $password,
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => 'incorrect',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('email.0')
                ->missingAll(['password', 'device'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_sign_in_blocked_user()
    {
        $email = time() . '@test.test';
        $password = 'test1234';
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => $password,
            'repeat_password' => $password,
            'name' => 'test',
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertOk();

        $user = \App\Models\User::where('email', $email)->firstOrFail();
        $user->status = UserStatus::Blocked;
        $user->save();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => $password,
            'device' => UserDevice::Web,
            'device_id' => uniqid(),
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('email.0')
                ->missingAll(['password', 'device'])
            )
            ->has('message')
            ->missing('user')
        );
    }

    public function test_profile_without_token(): void
    {
        $response = $this->get('/api/v1/user/profile');
        $response->assertUnauthorized();
    }

    public function test_profile_with_fake_token(): void
    {
        $response = $this->withJwtToken('fake_token')->get('/api/v1/user/profile');
        $response->assertUnauthorized();
    }

    public function test_profile_with_incorrect_token(): void
    {
        $response = $this->withJwtToken($this->getJwtToken(tokenType: JwtTokenType::Refresh))->get('/api/v1/user/profile');
        $response->assertUnauthorized();
    }

    public function test_profile_with_correct_token(): void
    {
        $response = $this->withJwtToken()->getJson('/api/v1/user/profile');
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->hasAll(['id', 'email', 'name', 'status', 'avatar'])
            ->missing('errors')
        );
    }

    public function test_refresh_token_without_token(): void
    {
        $response = $this->post('/api/v1/user/refresh-token');
        $response->assertUnauthorized();
    }

    public function test_refresh_token_with_fake_token(): void
    {
        $response = $this->withCredentials()->withUnencryptedCookie('refresh_token', 'fake_token')->post('/api/v1/user/refresh-token');
        $response->assertUnauthorized();
    }

    public function test_refresh_token_with_incorrect_token(): void
    {
        $response = $this->withCredentials()->withUnencryptedCookie('refresh_token', $this->getJwtToken())->post('/api/v1/user/refresh-token');
        $response->assertUnauthorized();
    }

    public function test_refresh_token_with_correct_data()
    {
        $response = $this->withCredentials()->withUnencryptedCookie('refresh_token', $this->getJwtToken(tokenType: JwtTokenType::Refresh))->postJson('/api/v1/user/refresh-token');
        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('user', fn (AssertableJson $json) => $json
                ->hasAll(['id', 'email', 'name', 'status', 'avatar'])
            )
            ->whereType('token', 'string')
            ->missing('errors')
        )->assertCookie('refresh_token');
    }
}
