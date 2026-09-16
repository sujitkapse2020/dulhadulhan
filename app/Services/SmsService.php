<?php

namespace App\Services;

use App\Services\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService implements SmsServiceInterface
{
    public function send(string $mobile, string $message): void
    {
        $endpoint = config('services.sms.endpoint');

        if ($endpoint) {
            Http::withToken((string) config('services.sms.token'))
                ->post($endpoint, ['mobile' => $mobile, 'message' => $message])
                ->throw();

            return;
        }

        Log::warning('SMS endpoint is not configured', compact('mobile', 'message'));
    }
}