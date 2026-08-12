<?php

namespace App\Repositories;

use App\Models\LoginHistory;
use App\Models\User;
use App\Repositories\Contracts\LoginHistoryRepositoryInterface;

class LoginHistoryRepository implements LoginHistoryRepositoryInterface
{
    public function record(User $user, string $ip = null, string $userAgent = null): LoginHistory
    {
        return LoginHistory::create([
            'user_id' => $user->id,
            'ip' => $ip,
            'device' => $userAgent,
            'browser' => null,
            'os' => null,
            'login_time' => now(),
        ]);
    }
}

