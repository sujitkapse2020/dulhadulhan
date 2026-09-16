<?php

namespace App\Services\Contracts;

use App\DTOs\UserPhotoDTO;
use App\Models\UserPhoto;
use Illuminate\Support\Collection;

interface UserPhotoServiceInterface
{
    /** @return array<int, array{path: string, status: string}> */
    public function queueUpload(int $userId, UserPhotoDTO $photoData): array;
    public function upload(int $userId, UserPhotoDTO $photoData): Collection;
    public function getAll(int $userId): Collection;
    public function getDetails(int $userId, int $photoId): ?UserPhoto;
    public function setAsProfile(int $userId, int $photoId): ?UserPhoto;
    public function delete(int $userId, int $photoId): bool;
}