<?php

namespace App\Providers;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\AuthServiceInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserProfileRepository;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(UserProfileRepositoryInterface::class, UserProfileRepository::class);
    }
}