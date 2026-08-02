<?php

namespace Tests\Feature\Auth;

use App\Mail\WelcomeMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_welcome_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'mobile' => '+1234567890',
            'password' => 'StrongPass@123',
            'password_confirmation' => 'StrongPass@123',
            'date_of_birth' => '1990-01-01',
            'gender' => 'female',
            'terms_accepted' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Registration successful. Please verify your account.');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);

        Mail::assertSent(WelcomeMail::class, function ($mail) {
            return $mail->hasTo('jane@example.com');
        });
    }
}
