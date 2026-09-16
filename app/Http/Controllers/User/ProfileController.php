<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\DTOs\UpdateProfileDTO;
use App\Http\Requests\DeleteProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateProfileStatusRequest;
use App\Http\Resources\ProfileResource;
use App\Services\Contracts\UserProfileServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /*|--------------------------SUMMERY--------------------------------|*/
    /* This controller contain below functions and their workings
    ______________________________________________________________________
    | 1. | Get Profile - To get user profile details                      |
    ----------------------------------------------------------------------
    | 2. | Update Profile - To update user profile details                |
    ----------------------------------------------------------------------
    | 3. | Deactivate profile - To make profile active/deactive profile   |
    ----------------------------------------------------------------------
    | 4. | Delete Profile - To remove user profile                        |
    ______________________________________________________________________
    /*|--------------------------SUMMERY--------------------------------|*/

    public function __construct(private readonly UserProfileServiceInterface $profileService) {}

    public function show(Request $request, int $id): JsonResponse
    {
        $profile = $this->profileService->getProfile($id);

        return $profile
            ? response()->json(['data' => new ProfileResource($profile)])
            : response()->json(['message' => 'Profile not found.'], 404);
    }

    public function update(UpdateProfileRequest $request, int $id): JsonResponse
    {
        if ((int) $request->user()->id !== $id) {
            return response()->json(['message' => 'You can only update your own profile.'], 403);
        }

        $profile = $this->profileService->updateProfile($id, UpdateProfileDTO::fromArray($request->validated()));

        return $profile
            ? response()->json(['message' => 'Profile updated successfully.', 'data' => new ProfileResource($profile)])
            : response()->json(['message' => 'Profile not found.'], 404);
    }

    public function status(UpdateProfileStatusRequest $request): JsonResponse
    {
        $user = $this->profileService->updateStatus($request->user()->id, $request->validated('status'));

        return response()->json(['message' => 'Profile status updated successfully.', 'data' => ['status' => $user->status]]);
    }

    public function destroy(DeleteProfileRequest $request): JsonResponse
    {
        $deleted = $this->profileService->deleteProfile($request->user()->id, $request->validated('reason'));

        return $deleted
            ? response()->json(['message' => 'Profile deleted successfully.'])
            : response()->json(['message' => 'Profile not found.'], 404);
    }
}
