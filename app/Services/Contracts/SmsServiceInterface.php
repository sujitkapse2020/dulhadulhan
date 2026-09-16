<?php

namespace App\Services\Contracts;

interface SmsServiceInterface
{
    public function send(string $mobile, string $message): void;
}