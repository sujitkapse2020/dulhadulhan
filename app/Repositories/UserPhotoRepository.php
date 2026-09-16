<?php

namespace App\Repositories;

use App\DTOs\UserPhotoDTO;
use App\Models\UserPhoto;
use App\Repositories\Contracts\UserPhotoRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserPhotoRepository implements UserPhotoRepositoryInterface
{
    public function create(int $userId, UserPhotoDTO $photoData): Collection
    { 
        return $this->createFromTemporaryPaths(
            $userId,
            collect($photoData->photos)->map(fn (UploadedFile $photo): string => $photo->getRealPath())->all(),
        );
    }

    /** @param array<int, string> $temporaryPaths */
    public function createFromTemporaryPaths(int $userId, array $temporaryPaths): Collection
    {
        $photos = collect();
        $sortOrder = UserPhoto::where('user_id', $userId)->max('sort_order') + 1;

        foreach ($temporaryPaths as $temporaryPath) {
            $baseName = Str::uuid()->toString();
            $this->storeImageForFolder($userId, $temporaryPath, 'large', $baseName);
            $this->storeImageForFolder($userId, $temporaryPath, 'medium', $baseName);
            $this->storeImageForFolder($userId, $temporaryPath, 'thumbnail', $baseName);
                     
            $photos->push(UserPhoto::create([
                'user_id' => $userId,
                'image' => "{$baseName}.webp",
                'path' => "user_photo/{$userId}",
                'sort_order' => $sortOrder,
                'status' => 'pending',
            ]));

            $sortOrder++;
        }

        return $photos;
    }

    public function findByUserId(int $userId): Collection
    {
        return UserPhoto::where('user_id', $userId)->latest()->get();
    }

    public function findByIdForUser(int $photoId, int $userId): ?UserPhoto
    {
        return UserPhoto::where('id', $photoId)->where('user_id', $userId)->first();
    }

    public function setAsProfile(UserPhoto $photo, int $userId): UserPhoto
    {
        return DB::transaction(function () use ($photo, $userId): UserPhoto {
            UserPhoto::where('user_id', $userId)->update(['is_profile' => false]);
            $photo->update(['is_profile' => true]);

            return $photo->refresh();
        });
    }

    public function delete(UserPhoto $photo): void
    {
        $baseName = pathinfo($photo->image, PATHINFO_BASENAME);

        foreach (['thumbnail', 'medium', 'large'] as $folder) {
            Storage::disk('public')->delete("user_photo/{$photo->user_id}/{$folder}/{$baseName}");
        }
        $photo->delete();
    }

    private function storeImageForFolder(int $userId, string $sourcePath, string $folder, string $baseName): string
    {
        $disk = Storage::disk('public');
        $directory = "user_photo/{$userId}/{$folder}";
        $disk->makeDirectory($directory);

        $relativePath = "{$directory}/{$baseName}.webp";

        $resource = @imagecreatefromstring(file_get_contents($sourcePath));

        if ($resource === false) {
            throw new \RuntimeException('Invalid image file. Please upload a valid JPG, PNG, or WEBP image.');
        }

        $targetWidth = (int) config("user_photos.sizes.{$folder}.max_width");
        $resized = $this->resizeImage($resource, $targetWidth);
        $this->addWatermark($resized, $folder);
        $tempPath = tempnam(sys_get_temp_dir(), 'user-photo-');

        if ($tempPath === false) {
            imagedestroy($resource);
            throw new \RuntimeException('Unable to create temporary file for image processing.');
        }

        imagewebp($resized, $tempPath, (int) config("user_photos.sizes.{$folder}.webp_quality", 84));
        imagedestroy($resource);
        imagedestroy($resized);

        $disk->put($relativePath, fopen($tempPath, 'rb'));
        @unlink($tempPath);

        return $relativePath;
    }

    private function addWatermark($image, string $folder): void
    {
        $fontPath = collect(config('user_photos.watermark.font_paths', []))
            ->first(fn (string $path): bool => is_file($path));

        if ($fontPath === null) {
            throw new \RuntimeException('Unable to find the watermark font.');
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $fontSize = max(1, (int) round($width * config("user_photos.watermark.font_size_percent.{$folder}", 3) / 100));
        $horizontalMargin = max(1, (int) round($width * config("user_photos.watermark.horizontal_margin_percent.{$folder}", 3) / 100));
        $verticalMargin = max(1, (int) round($width * config("user_photos.watermark.vertical_margin_percent.{$folder}", 3) / 100));
        $text = (string) config("user_photos.watermark.text.{$folder}", 'DulhaDulhan');
        $box = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = abs($box[2] - $box[0]);
        $textHeight = abs($box[7] - $box[1]);
        $x = max($horizontalMargin, $width - $textWidth - $horizontalMargin);
        $y = max($textHeight + $verticalMargin, $height - $verticalMargin);

        imagealphablending($image, true);
        $textOpacity = config("user_photos.watermark.text_opacity_percent.{$folder}", 50);
        if (config("user_photos.watermark.shadow.{$folder}", false)) {
            $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, $this->gdAlpha(35));
            imagettftext($image, $fontSize, 0, $x + 1, $y + 1, $shadowColor, $fontPath, $text);
        }

        $textColor = imagecolorallocatealpha($image, 255, 255, 255, $this->gdAlpha($textOpacity));
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $text);
    }

    private function gdAlpha(int|float $opacityPercent): int
    {
        return (int) round(127 * (1 - ($opacityPercent / 100)));
    }

    private function resizeImage($resource, int $maxWidth)
    {
        $sourceWidth = imagesx($resource);
        $sourceHeight = imagesy($resource);

        if ($sourceWidth <= $maxWidth) {
            $targetWidth = $sourceWidth;
            $targetHeight = $sourceHeight;
        } else {
            $ratio = $maxWidth / $sourceWidth;
            $targetWidth = (int) round($sourceWidth * $ratio);
            $targetHeight = (int) round($sourceHeight * $ratio);
        }

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $resource, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        return $resized;
    }
}