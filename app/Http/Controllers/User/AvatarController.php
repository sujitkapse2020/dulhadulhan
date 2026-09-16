<?php

namespace App\Http\Controllers\User;

use App\Exceptions\PhotoFaceValidationException;
use App\Http\Controllers\Controller;
use App\DTOs\UserPhotoDTO;
use App\Http\Requests\UserPhotoRequest;
use App\Services\Contracts\UserPhotoServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
     /*|--------------------------SUMMERY--------------------------------|*/
    /* This controller contain below functions and their workings
    ______________________________________________________________________
    | 1. | Get Profile Picture - To get user profile picture details      |
    ----------------------------------------------------------------------
    | 2. | Upload Profile Picture - To upload user profile picture        |
    ----------------------------------------------------------------------
    | 3. | Delete Profile Picture - To remove user profile picture        |
    ---------------------------------------------------------------------------- 
    | 4. | Set Profile Picture - To set user profile picture as default picture | 
    -------------------------------------------------------------------------------------  
    | 5. | Get Profile Picture Details - To get user profile picture details by photo id |
    _____________________________________________________________________________________
    /*|--------------------------SUMMERY--------------------------------|*/

    public function __construct(private readonly UserPhotoServiceInterface $photoService) {}

    public function getProfilePicture(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->photoService->getAll($request->user()->id)]);
    }

    public function uploadProfilePicture(UserPhotoRequest $request): JsonResponse
    {
        try {
            $temporaryUploads = $this->photoService->queueUpload(
                $request->user()->id,
                UserPhotoDTO::fromArray($request->validated()),
            );
        } catch (PhotoFaceValidationException $exception) {
            $status = str_contains($exception->getMessage(), 'not detected') ? 422 : 503;

            return response()->json(['message' => $exception->getMessage()], $status);
        }

        return response()->json([
            'message' => 'Profile pictures uploaded successfully and queued for processing.',
            'data' => $temporaryUploads,
        ], 202);
    }

    public function deleteProfilePicture(Request $request, int $photoId): JsonResponse
    {
        return $this->photoService->delete($request->user()->id, $photoId)
            ? response()->json(['message' => 'Profile picture deleted successfully.'])
            : response()->json(['message' => 'Profile picture not found.'], 404);
    }

    public function setProfilePicture(Request $request, int $photoId): JsonResponse
    {
        $photo = $this->photoService->setAsProfile($request->user()->id, $photoId);

        return $photo
            ? response()->json(['message' => 'Profile picture set successfully.', 'data' => $photo])
            : response()->json(['message' => 'Profile picture not found.'], 404);
    }

    public function getProfilePictureDetails(Request $request, int $photoId): JsonResponse
    {
        $photo = $this->photoService->getDetails($request->user()->id, $photoId);

        return $photo
            ? response()->json(['data' => $photo])
            : response()->json(['message' => 'Profile picture not found.'], 404);
    }

    
}
