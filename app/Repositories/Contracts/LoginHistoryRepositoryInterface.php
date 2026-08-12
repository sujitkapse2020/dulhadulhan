<?php

namespace App\Repositories\Contracts;

use App\Models\LoginHistory;
use App\Models\User;

interface LoginHistoryRepositoryInterface
{
    public function record(User $user, string $ip = null, string $userAgent = null): LoginHistory;
}

