<?php

namespace Tests\Feature\Auth;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobileVerificationTest extends TestCase
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
        ]);
    }

    public function test_user_can_request_mobile_otp(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/api/auth/mobile/send-otp', [
            'user_id' => $user->id,
            'mobile' => '9876543210',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'OTP sent successfully.')
            ->assertJsonStructure([
                'message',
                'data' => [
                    'otp' => ['*']
                ]
            ]);

        $this->assertDatabaseHas('otps', [
            'user_id' => $user->id,
            'mobile' => '9876543210',
        ]);
    }

    public function test_user_can_verify_mobile_otp(): void
    {
        $user = $this->createUser();

        $otp = Otp::create([
            'user_id' => $user->id,
            'mobile' => '9876543210',
            'otp' => '123456',
            'expires_at' => now()->addMinutes(2),
            'verified' => false,
        ]);

        $response = $this->postJson('/api/auth/mobile/verify-otp', [
            'user_id' => $user->id,
            'mobile' => '9876543210',
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Mobile number verified successfully.');

        $this->assertDatabaseHas('otps', [
            'user_id' => $user->id,
            'mobile' => '9876543210',
            'verified' => true,
        ]);

        $this->assertNotNull($user->fresh()->mobile_verified_at);
    }
}
