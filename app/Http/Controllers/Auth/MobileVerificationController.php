<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\SendMobileOtpDTO;
use App\DTOs\VerifyOtpDTO;
use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMobileOtpRequest;
use App\Http\Requests\VerifyMobileOtpRequest;
use App\Services\Contracts\MobileVerificationServiceInterface;
use Illuminate\Http\JsonResponse;

class MobileVerificationController extends Controller
{
    public function __construct(private readonly MobileVerificationServiceInterface $service) {}

    public function send(SendMobileOtpRequest $request): JsonResponse
    {
        $otp = $this->service->sendOtp($request->user()->id,SendMobileOtpDTO::fromArray($request->validated()));

        return response()->json([
            'message' => 'OTP sent successfully.',
            'data' => ['otp' => [$otp]],
        ]);
    }

    public function verify(VerifyMobileOtpRequest $request): JsonResponse
    {
        try {
            $this->service->verifyOtp($request->user()->id,VerifyOtpDTO::fromArray($request->validated()));
            return response()->json(['message' => 'Mobile number verified successfully.']);
        } catch (InvalidCredentialsException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }
}