<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'profile_no' => 'DDTEST0001',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'mobile' => '9876543210',
            'password' => Hash::make('StrongPass@123'),
            'email_verified_at' => now(),
        ]);
    }

    public function test_user_can_login_with_email(): void
    {
        $this->createUser();

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/126.0.0.0 Safari/537.36';

        $response = $this->withHeaders(['User-Agent' => $userAgent])->postJson('/api/auth/login', [
            'login' => 'jane@example.com',
            'password' => 'StrongPass@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseHas('login_histories', [
            'user_id' => User::where('email', 'jane@example.com')->value('id'),
            'device' => $userAgent,
            'browser' => 'Chrome/126.0.0.0',
            'os' => 'Windows 10/11',
        ]);
    }

    public function test_user_can_login_with_mobile(): void
    {
        $this->createUser();

        $response = $this->postJson('/api/auth/login', [
            'login' => '9876543210',
            'password' => 'StrongPass@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Login successful.');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->createUser();

        $response = $this->postJson('/api/auth/login', [
            'login' => 'jane@example.com',
            'password' => 'WrongPass@123',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Invalid credentials.');
    }

    public function test_login_requires_valid_fields(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login', 'password']);
    }
}

