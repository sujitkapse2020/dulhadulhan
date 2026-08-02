<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\UserDTO;
use App\DTOs\UserProfileDTO;
use App\Exceptions\DuplicateAccountException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Throwable;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ) {}

    public function __invoke(RegisterUserRequest $request): JsonResponse
    { 
        $userDTO = UserDTO::fromArray($request->validated());
        $userProfileDTO = UserProfileDTO::fromArray($request->validated());
        
        try {
            $user = $this->authService->register($userDTO);
            $userProfile = $this->authService->createProfile($userProfileDTO, $user->id);

            return response()->json([
                'message' => 'Registration successful. Please verify your account.',
                'data' => new UserResource($user),
            ], 201);
        } catch (DuplicateAccountException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (Throwable $e) {
            Log::error('User registration failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Registration failed. Please try again.'], 500);
        }
    }

}