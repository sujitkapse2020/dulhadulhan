<?php

namespace App\Jobs;

use App\Repositories\Contracts\UserPhotoRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessUserPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param array<int, string> $temporaryPaths */
    public function __construct(
        public readonly int $userId,
        public readonly array $temporaryPaths,
    ) {}

    public function handle(UserPhotoRepositoryInterface $photoRepository): void
    {
        $disk = Storage::disk('public');
        $absolutePaths = array_map(fn (string $path): string => $disk->path($path), $this->temporaryPaths);

        $photoRepository->createFromTemporaryPaths($this->userId, $absolutePaths);
        $disk->delete($this->temporaryPaths);
    }
}