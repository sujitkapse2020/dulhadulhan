<?php

namespace App\Services;

use App\Exceptions\PhotoFaceValidationException;
use App\DTOs\UserPhotoDTO;
use App\Jobs\ProcessUserPhotos;
use App\Models\UserPhoto;
use App\Repositories\Contracts\UserPhotoRepositoryInterface;
use App\Services\Contracts\UserPhotoServiceInterface;
use App\Services\Contracts\FaceDetectionServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UserPhotoService implements UserPhotoServiceInterface
{
    public function __construct(
        private readonly UserPhotoRepositoryInterface $photoRepository,
        private readonly FaceDetectionServiceInterface $faceDetector,
    ) {}

    public function queueUpload(int $userId, UserPhotoDTO $photoData): array
    {
        $directory = "user_photo/temp/{$userId}";
        $temporaryUploads = [];

        // foreach ($photoData->photos as $photo) {

        //     $temporaryPath = $photo->getRealPath();
        //     $faceDetectionResult = $this->faceDetector->detect($temporaryPath);

        //     if ($faceDetectionResult->faceCount === 0) {
        //         throw new PhotoFaceValidationException('No face detected in the uploaded photo.');
        //     }

        //     if ($faceDetectionResult->faceCount > 1) {
        //         throw new PhotoFaceValidationException('Multiple faces detected in the uploaded photo.');
        //     }
        // }

        foreach ($photoData->photos as $photo) {
            $extension = strtolower($photo->getClientOriginalExtension()) ?: 'jpg';
            $path = $photo->storeAs($directory, Str::uuid().'.'.$extension, 'public');
            $temporaryUploads[] = ['path' => $path, 'status' => 'queued'];
        }

        ProcessUserPhotos::dispatch($userId, array_column($temporaryUploads, 'path'));

        return $temporaryUploads;
    }

    public function upload(int $userId, UserPhotoDTO $photoData): Collection
    {
        return $this->photoRepository->create($userId, $photoData);
    }

    public function getAll(int $userId): Collection
    {
        return $this->photoRepository->findByUserId($userId);
    }

    public function getDetails(int $userId, int $photoId): ?UserPhoto
    {
        return $this->photoRepository->findByIdForUser($photoId, $userId);
    }

    public function setAsProfile(int $userId, int $photoId): ?UserPhoto
    {
        $photo = $this->photoRepository->findByIdForUser($photoId, $userId);

        return $photo ? $this->photoRepository->setAsProfile($photo, $userId) : null;
    }

    public function delete(int $userId, int $photoId): bool
    {
        $photo = $this->photoRepository->findByIdForUser($photoId, $userId);

        if (! $photo) {
            return false;
        }

        $this->photoRepository->delete($photo);

        return true;
    }
}