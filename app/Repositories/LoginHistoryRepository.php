<?php

namespace App\Repositories;

use App\Models\LoginHistory;
use App\Models\User;
use App\Repositories\Contracts\LoginHistoryRepositoryInterface;

class LoginHistoryRepository implements LoginHistoryRepositoryInterface
{
    public function record(User $user, string $ip = null, string $userAgent = null): LoginHistory
    {
        [$browser, $os] = $this->parseUserAgent($userAgent);

        return LoginHistory::create([
            'user_id' => $user->id,
            'ip' => $ip,
            'device' => $userAgent,
            'browser' => $browser,
            'os' => $os,
            'login_time' => now(),
        ]);
    }

    private function parseUserAgent(?string $userAgent): array
    {
        $browser = null;
        $os = null;

        if ($userAgent === null) {
            return [$browser, $os];
        }

        if (preg_match('~(?:Edg|Edge)/[\d.]+~i', $userAgent, $matches)) {
            $browser = $matches[0];
        } elseif (preg_match('~(?:Chrome|Firefox|Version|CriOS|FxiOS)/[\d.]+~i', $userAgent, $matches)) {
            $browser = $matches[0];
        } elseif (preg_match('~(?:MSIE |Trident/.*rv:)([\d.]+)~i', $userAgent, $matches)) {
            $browser = 'Internet Explorer/'.$matches[1];
        }

        if (preg_match('~Windows NT ([\d.]+)~i', $userAgent, $matches)) {
            $os = match ($matches[1]) {
                '10.0' => 'Windows 10/11',
                '6.3' => 'Windows 8.1',
                '6.2' => 'Windows 8',
                '6.1' => 'Windows 7',
                default => 'Windows',
            };
        } elseif (preg_match('~(?:iPhone|CPU iPhone OS|CPU OS) ([\d_]+)~i', $userAgent, $matches)) {
            $os = 'iOS '.$this->normalizeVersion($matches[1]);
        } elseif (preg_match('~Android[ -]?([\d.]+)~i', $userAgent, $matches)) {
            $os = 'Android '.$matches[1];
        } elseif (preg_match('~Mac OS X[ /]([\d_.]+)~i', $userAgent, $matches)) {
            $os = 'macOS '.$this->normalizeVersion($matches[1]);
        } elseif (preg_match('~Linux~i', $userAgent)) {
            $os = 'Linux';
        }

        return [$browser, $os];
    }

    private function normalizeVersion(string $version): string
    {
        return str_replace('_', '.', $version);
    }
}

