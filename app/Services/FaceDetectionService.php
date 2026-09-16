<?php

namespace App\Services;

use App\DTOs\FaceDetectionDTO;
use App\Exceptions\PhotoFaceValidationException;
use App\Services\Contracts\FaceDetectionServiceInterface;
use svay\FaceDetector;

class FaceDetectionService implements FaceDetectionServiceInterface
{
    private ?FaceDetector $detector = null;

    public function detect(string $imagePath): FaceDetectionDTO
    {
        $detectionData = config('face_detection.data');
        $imageData = @file_get_contents($imagePath);

        if (! is_string($detectionData) || ! is_file($detectionData)) {
            throw new PhotoFaceValidationException('Face detection is not configured. Please contact the administrator.');
        }

        if ($imageData === false) {
            throw new PhotoFaceValidationException('Face detection failed. Please upload a clear photo and try again.');
        }

        try {
            $this->detector ??= new FaceDetector($detectionData);
            $hasFace = $this->detector->faceDetect($imageData);
        } catch (\Throwable $exception) {
            report($exception);

            throw new PhotoFaceValidationException(
                'Face detection failed. Please upload a clear photo and try again.',
                previous: $exception,
            );
        }

        return new FaceDetectionDTO(faceCount: $hasFace ? 1 : 0);
    }
}