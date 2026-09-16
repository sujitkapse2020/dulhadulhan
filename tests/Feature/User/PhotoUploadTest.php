<?php

namespace Tests\Feature\User;

use App\Jobs\ProcessUserPhotos;
use App\Models\User;
use App\Models\UserPhoto;
use App\Repositories\Contracts\UserPhotoRepositoryInterface;
use App\Services\Contracts\UserPhotoFaceDetectorInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(UserPhotoFaceDetectorInterface::class, new class implements UserPhotoFaceDetectorInterface
        {
            public function hasSingleFace(string $imagePath): bool
            {
                return true;
            }
        });
    }

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

    public function test_user_can_upload_jpg_photo_as_webp_in_size_folders(): void
    {
        Storage::fake('public');
        Queue::fake();
        $user = $this->createUser();

        $file = UploadedFile::fake()->image('profile.jpg', 1600, 1200);

        $response = $this->actingAs($user)->postJson('/api/user/photos', [
            'photo' => [$file],
        ]);

        $response->assertStatus(202);
        Queue::assertPushed(ProcessUserPhotos::class, function (ProcessUserPhotos $job) use ($user, &$queuedJob): bool {
            $queuedJob = $job;

            return $job->userId === $user->id;
        });

        $queuedJob->handle(app(UserPhotoRepositoryInterface::class));

        $storedPhoto = UserPhoto::firstOrFail();

        $this->assertStringEndsWith('.webp', $storedPhoto->image);

        $largePath = "{$storedPhoto->path}/large/{$storedPhoto->image}";
        $mediumPath = "{$storedPhoto->path}/medium/{$storedPhoto->image}";
        $thumbnailPath = "{$storedPhoto->path}/thumbnail/{$storedPhoto->image}";

        Storage::disk('public')->assertExists($largePath);
        Storage::disk('public')->assertExists($mediumPath);
        Storage::disk('public')->assertExists($thumbnailPath);

        $largeImage = imagecreatefromwebp(Storage::disk('public')->path($largePath));
        $this->assertNotFalse($largeImage);
        $watermarkPixels = 0;
        for ($x = imagesx($largeImage) - 100; $x < imagesx($largeImage) - 30; $x++) {
            for ($y = imagesy($largeImage) - 100; $y < imagesy($largeImage) - 30; $y++) {
                $pixel = imagecolorat($largeImage, $x, $y);
                $watermarkPixels += (($pixel & 0xFFFFFF) > 0) ? 1 : 0;
            }
        }

        $this->assertGreaterThan(0, $watermarkPixels);

        $mediumImage = imagecreatefromwebp(Storage::disk('public')->path($mediumPath));
        $thumbnailImage = imagecreatefromwebp(Storage::disk('public')->path($thumbnailPath));
        $this->assertLessThanOrEqual(1200, imagesx($largeImage));
        $this->assertLessThanOrEqual(600, imagesx($mediumImage));
        $this->assertLessThanOrEqual(200, imagesx($thumbnailImage));
        imagedestroy($largeImage);
        imagedestroy($mediumImage);
        imagedestroy($thumbnailImage);
    }

    public function test_user_can_upload_webp_photo_directly(): void
    {
        Storage::fake('public');
        Queue::fake();
        $user = $this->createUser();

        $image = imagecreatetruecolor(800, 600);
        imagecolorallocate($image, 0, 0, 0);
        $tempFile = tempnam(sys_get_temp_dir(), 'webp-upload');
        imagewebp($image, $tempFile, 90);
        imagedestroy($image);

        $file = UploadedFile::fake()->createWithContent('profile.webp', file_get_contents($tempFile), 'image/webp');
        unlink($tempFile);

        $response = $this->actingAs($user)->postJson('/api/user/photos', [
            'photo' => [$file],
        ]);

        $response->assertStatus(202);
        Queue::assertPushed(ProcessUserPhotos::class);

        $this->assertDatabaseCount('user_photos', 0);
    }

    public function test_photo_without_one_detected_face_is_rejected_before_queueing(): void
    {
        Storage::fake('public');
        Queue::fake();
        $this->app->instance(UserPhotoFaceDetectorInterface::class, new class implements UserPhotoFaceDetectorInterface
        {
            public function hasSingleFace(string $imagePath): bool
            {
                return false;
            }
        });

        $user = $this->createUser();
        $response = $this->actingAs($user)->postJson('/api/user/photos', [
            'photo' => [UploadedFile::fake()->image('group.jpg', 1600, 1200)],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Face is not detected properly. Please upload a clear single-person photo.');
        Queue::assertNothingPushed();
        $this->assertDatabaseCount('user_photos', 0);
    }
}
