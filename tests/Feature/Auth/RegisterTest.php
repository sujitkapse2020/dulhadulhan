<?php

namespace Tests\Feature\Auth;

use App\Mail\WelcomeMail;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
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
            'mobile' => '1234567890',
            'password' => 'StrongPass@123',
            'password_confirmation' => 'StrongPass@123',
            'date_of_birth' => '1990-01-01',
            'gender' => 'female',
            'terms_accepted' => 'accepted',
            'profile_for' => 'self',
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

    public function test_user_registration_sends_verification_notification(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john@example.com',
            'mobile' => '1987654321',
            'password' => 'StrongPass@123',
            'password_confirmation' => 'StrongPass@123',
            'date_of_birth' => '1992-02-02',
            'gender' => 'male',
            'terms_accepted' => 'accepted',
            'profile_for' => 'self',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'john@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }
}
