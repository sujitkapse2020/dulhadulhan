<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\DTOs\NotificationDTO;
use App\Models\User;
use App\Repositories\Contracts\NotificationReporitoryInterface;
use App\Notifications\VerifyEmailNotification;

class NotificationController extends Controller
{
    public function __construct(protected NotificationReporitoryInterface $notifications)
    {
    }

    public function sendEmailVerificationNotification(User $user)
    {
        if ($user && !$user->hasVerifiedEmail()) {
            // send our custom verify email notification (queueable)
            $user->notify(new VerifyEmailNotification());

            // prepare DTO and persist a record via repository after DB commit
            $dto = new NotificationDTO(
                $user->id,
                'Verify Your Email Address - DulhaDulhan',
                'Please verify your email address to activate your account and start creating your matrimonial profile.',
                VerifyEmailNotification::class,
                false
            );

            DB::afterCommit(function () use ($dto) {
                $this->notifications->create($dto);
            });

            return response()->json(['message' => 'Verification email sent.']);
        }

        return response()->json(['message' => 'User not found or already verified.'], 400);
    }
}
