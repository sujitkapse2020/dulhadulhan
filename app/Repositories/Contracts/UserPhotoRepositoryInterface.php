<?php

namespace App\Repositories\Contracts;

use App\DTOs\UserPhotoDTO;
use App\Models\UserPhoto;
use Illuminate\Support\Collection;

interface UserPhotoRepositoryInterface
{
    public function create(int $userId, UserPhotoDTO $photoData): Collection;
    /** @param array<int, string> $temporaryPaths */
    public function createFromTemporaryPaths(int $userId, array $temporaryPaths): Collection;
    public function findByUserId(int $userId): Collection;
    public function findByIdForUser(int $photoId, int $userId): ?UserPhoto;
    public function setAsProfile(UserPhoto $photo, int $userId): UserPhoto;
    public function delete(UserPhoto $photo): void;
}