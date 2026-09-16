<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MobileVerificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\AvatarController;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    $password = 'Sujit@91';
    $hashedPassword = \Hash::make($password);
    DB::table('users')->where('id', 2)->update(['password' => $hashedPassword]);
    return $hashedPassword;
});

Route::get('/csrf-token', function () {
        $token = csrf_token();
        return response()->json(['csrf_token' => $token])->header('Cache-Control', 'no-store, private');
    });

/*|--------------------------------------------------------------------------AUTH ROUTES--------------------------------------------------------------------------|*/
Route::prefix('api')->group(function () {
    Route::post('/auth/register', RegisterController::class)->middleware('throttle:5,1');
    Route::post('/auth/login', LoginController::class)->middleware('throttle:5,1');
    
    Route::group(['prefix' => 'auth/mobile', 'middleware' => 'auth:sanctum'], function () {
        Route::post('/send-otp', [MobileVerificationController::class, 'send'])->middleware('throttle:5,1');;
        Route::post('/verify-otp', [MobileVerificationController::class, 'verify'])->middleware('throttle:10,1');
    });

    Route::group(['prefix'=>'user/profile','middleware'=>'auth:sanctum'],function(){
        Route::patch('/status', [ProfileController::class, 'status']);
        Route::get('/{id}', [ProfileController::class, 'show']);
        Route::match(['put', 'patch'], '/{id}', [ProfileController::class, 'update']);
        Route::delete('/', [ProfileController::class, 'destroy']);
    });

    Route::group(['prefix' => 'user/photos', 'middleware' => 'auth:sanctum'], function () {
        Route::get('/', [AvatarController::class, 'getProfilePicture']);
        Route::post('/', [AvatarController::class, 'uploadProfilePicture']);
        Route::get('/{photoId}', [AvatarController::class, 'getProfilePictureDetails']);
        Route::patch('/{photoId}/default', [AvatarController::class, 'setProfilePicture']);
        Route::delete('/{photoId}', [AvatarController::class, 'deleteProfilePicture']);
    });
    
    });
/*|--------------------------------------------------------------------------AUTH ROUTES--------------------------------------------------------------------------|*/

Route::get('/email/verify/{id}/{hash}', EmailVerificationController::class)->name('verification.verify');

Route::get('/delete/{id}', function ($id) {
    $userId = $id;
    DB::transaction(function () use ($userId) {           
            DB::table('profiles')->where('user_id', $userId)->delete();
            DB::table('users')->where('id', $userId)->delete();
        });
    return response()->json(['message' => 'User and profile deleted successfully.']);
});

