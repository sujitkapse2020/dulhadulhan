<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\UserRegistered;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Support\Facades\Event;
use App\Repositories\Contracts\NotificationReporitoryInterface;
use App\Repositories\NotificationRepository;
use App\Repositories\Contracts\OtpRepositoryInterface;
use App\Repositories\OtpRepository;
use App\Services\Contracts\MobileVerificationServiceInterface;
use App\Services\Contracts\SmsServiceInterface;
use App\Services\MobileVerificationService;
use App\Services\SmsService;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Repositories\UserProfileRepository;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Services\UserProfileService;
use App\Repositories\Contracts\UserPhotoRepositoryInterface;
use App\Repositories\UserPhotoRepository;
use App\Services\Contracts\UserPhotoServiceInterface;
use App\Services\UserPhotoService;
use App\Services\Contracts\FaceDetectionServiceInterface;
use App\Services\FaceDetectionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NotificationReporitoryInterface::class, NotificationRepository::class);
        $this->app->bind(OtpRepositoryInterface::class, OtpRepository::class);
        $this->app->bind(MobileVerificationServiceInterface::class, MobileVerificationService::class);
        $this->app->bind(SmsServiceInterface::class, SmsService::class);
        $this->app->bind(UserProfileRepositoryInterface::class, UserProfileRepository::class);
        $this->app->bind(UserProfileServiceInterface::class, UserProfileService::class);
        $this->app->bind(UserPhotoRepositoryInterface::class, UserPhotoRepository::class);
        $this->app->bind(UserPhotoServiceInterface::class, UserPhotoService::class);
        $this->app->bind(FaceDetectionServiceInterface::class, FaceDetectionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(UserRegistered::class, SendWelcomeEmail::class);
    }
}
