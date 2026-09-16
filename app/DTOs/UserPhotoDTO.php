<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

final class UserPhotoDTO
{
    /**
     * @param array<int, UploadedFile> $photos
     */
    public function __construct(
        public readonly array $photos,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self($data['photo']);
    }
}