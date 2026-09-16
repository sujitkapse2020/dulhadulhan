<?php

namespace App\DTOs;

final readonly class FaceDetectionDTO
{
    public function __construct(
        public int $faceCount,
        public array $faces = [],
    ) {}

    public function hasSingleFace(): bool
    {
        return $this->faceCount === 1;
    }

    public function hasMultipleFaces(): bool
    {
        return $this->faceCount > 1;
    }

    public function hasNoFace(): bool
    {
        return $this->faceCount === 0;
    }
}