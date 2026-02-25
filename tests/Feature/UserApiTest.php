<?php

namespace Tests\Feature;

use App\Enums\UserDevice;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use RonasIT\AutoDoc\Traits\AutoDocTestCaseTrait;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use AutoDocTestCaseTrait;
    use DatabaseTransactions;

    public function test_sign_up_empty_data()
    {
        $response = $this->postJson('/api/v1/user/sign-up', []);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->hasAll(['email.0', 'password.0', 'repeat_password.0', 'name.0', 'device.0'])
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

    public function test_sign_up_incorrect_password()
    {
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => 'test@test.test',
            'password' => 'test',
            'repeat_password' => 'test1234',
            'name' => 'test',
            'device' => UserDevice::Web,
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

    public function test_sign_up_incorrect_name()
    {
        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => 'test@test.test',
            'password' => 'test1234',
            'repeat_password' => 'test1234',
            'name' => '',
            'device' => UserDevice::Web,
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('name.0')
                ->missingAll(['email', 'repeat_password', 'password', 'device'])
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
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('errors', fn (AssertableJson $json) => $json
                ->has('device.0')
                ->missingAll(['email', 'repeat_password', 'password', 'name'])
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
            ->whereType('refreshToken', 'string')
            ->missing('errors')
        );
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
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-up', [
            'email' => $email,
            'password' => 'test1234another',
            'repeat_password' => 'test1234another',
            'name' => 'test another',
            'device' => UserDevice::Web,
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
                ->hasAll(['email.0', 'password.0', 'device.0'])
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
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => $password,
            'device' => 'fake_device',
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
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => $password,
            'device' => UserDevice::Web,
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
            ->whereType('refreshToken', 'string')
            ->missing('errors')
        );
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
        ]);
        $response->assertOk();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => 'incorrect',
            'device' => UserDevice::Web,
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
        ]);
        $response->assertOk();

        $user = \App\Models\User::where('email', $email)->firstOrFail();
        $user->status = UserStatus::Blocked;
        $user->save();

        $response = $this->postJson('/api/v1/user/sign-in', [
            'email' => $email,
            'password' => $password,
            'device' => UserDevice::Web,
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

}
