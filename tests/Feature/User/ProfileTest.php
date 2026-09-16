<?php

namespace Tests\Feature\User;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'uuid' => Str::uuid(),
            'profile_no' => 'DUL-'.Str::random(8),
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'mobile' => '9876543210',
            'password' => Hash::make('password'),
        ]);
    }

    private function createProfile(User $user): Profile
    {
        return Profile::create([
            'user_id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'dob' => '1995-01-01',
            'profile_for' => 'self',
        ]);
    }

    public function test_user_can_get_profile_by_user_id(): void
    {
        $user = $this->createUser();
        $profile = $this->createProfile($user);

        $this->actingAs($user)->getJson("/api/user/profile/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $profile->id)
            ->assertJsonPath('data.first_name', 'Jane');
    }

    public function test_user_profile_is_cached_and_invalidated_on_update(): void
    {
        $user = $this->createUser();
        $this->createProfile($user);

        $this->actingAs($user)->getJson("/api/user/profile/{$user->id}")
            ->assertOk();

        $this->assertTrue(Cache::store('redis')->has("user_profile:{$user->id}"));

        $this->actingAs($user)->patchJson("/api/user/profile/{$user->id}", [
            'first_name' => 'Janet',
            'about_me' => 'A short introduction.',
            'date_of_birth' => '1994-02-03',
        ])->assertOk()->assertJsonPath('data.first_name', 'Janet');

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'first_name' => 'Janet',
            'dob' => '1994-02-03',
        ]);

        $this->actingAs($user)->getJson("/api/user/profile/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Janet');
    }

    public function test_user_can_change_status_and_delete_with_reason(): void
    {
        $user = $this->createUser();
        $this->createProfile($user);
        $this->actingAs($user)->patchJson('/api/user/profile/status', ['status' => 'inactive'])
            ->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'inactive']);

        $this->actingAs($user)->deleteJson('/api/user/profile', ['reason' => 'No longer using the service.'])
            ->assertOk();

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
            'delete_reason' => 'No longer using the service.',
        ]);
        $this->assertDatabaseMissing('profiles', ['user_id' => $user->id]);
    }

    public function test_delete_requires_a_reason(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->deleteJson('/api/user/profile')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['reason']);
    }
}