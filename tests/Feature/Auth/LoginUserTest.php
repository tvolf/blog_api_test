<?php

namespace Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_login_successfully(): void
    {
        $route = route('auth.login');

        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'secret'
        ]);

        $data = [
            'email' => 'john@example.com',
            'password' => 'secret'
        ];

        $this->postJson($route, $data)
            ->assertStatus(200);
    }

    #[Test]
    public function user_with_non_existed_email_is_not_allowed_to_login(): void
    {
        $route = route('auth.login');

        User::factory()->create([
            'email' => 'mark@example.com',
            'password' => 'secret'
        ]);

        $data = [
            'email' => 'john@example.com',
            'password' => 'secret'
        ];

        $this->postJson($route, $data)
            ->assertJsonValidationErrors(['email'])
            ->assertStatus(422);
    }

    #[Test]
    public function user_with_non_matched_password_is_not_allowed_to_login(): void
    {
        $route = route('auth.login');

        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password'
        ]);

        $data = [
            'email' => 'john@example.com',
            'password' => 'secret'
        ];

        $this->postJson($route, $data)
            ->assertJsonValidationErrors(['email'])
            ->assertStatus(422);
    }
}
