<?php

namespace App\Services\Contracts;

use App\DTOs\FaceDetectionDTO;

interface FaceDetectionServiceInterface
{
    public function detect(string $imagePath): FaceDetectionDTO;
}