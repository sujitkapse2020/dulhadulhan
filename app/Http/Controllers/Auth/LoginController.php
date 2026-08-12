<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\LoginDTO;
use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ) {}

    public function __invoke(LoginUserRequest $request): JsonResponse
    {
        $loginDTO = LoginDTO::fromArray($request->validated());

        try {
            $result = $this->authService->login(
                $loginDTO,
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'message' => 'Login successful.',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ],
            ], 200);
        } catch (InvalidCredentialsException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (Throwable $e) {
            Log::error('User login failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Login failed. Please try again.'], 500);
        }
    }
}

