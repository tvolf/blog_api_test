<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_register_successfully(): void
    {
        $route = route('auth.register');

        $data = [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $olcUsersCount = User::count();

        $this->postJson($route, $data)
            ->assertStatus(201);

        $this->assertEquals($olcUsersCount + 1, User::count());
    }

    #[Test]
    public function user_with_existed_email_register_is_blocked(): void
    {
        $existedEmail = 'test@example.com';

        User::factory()->create([
            'email' => $existedEmail
        ]);

        $route = route('auth.register');

        $data = [
            'name' => 'John',
            'email' => $existedEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $olcUsersCount = User::count();

        $this->postJson($route, $data)
            ->assertStatus(422);

        $this->assertEquals($olcUsersCount, User::count());
    }

    #[Test]
    public function password_length_is_less_6_chars(): void
    {
        $existedEmail = 'test@example.com';

        $route = route('auth.register');

        $data = [
            'name' => 'John',
            'email' => $existedEmail,
            'password' => '12345',
            'password_confirmation' => '12345',
        ];

        $olcUsersCount = User::count();

        $this->postJson($route, $data)
            ->assertJsonValidationErrors(['password'])
            ->assertStatus(422);

        $this->assertEquals($olcUsersCount, User::count());
    }
}
