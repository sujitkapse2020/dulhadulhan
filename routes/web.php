<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/csrf-token', function () {
        $token = csrf_token();
        return response()->json(['csrf_token' => $token])->header('Cache-Control', 'no-store, private');
    });

/*|--------------------------------------------------------------------------AUTH ROUTES--------------------------------------------------------------------------|*/
Route::prefix('api')->group(function () {
    Route::post('/auth/register', RegisterController::class)->middleware('throttle:5,1');
});
/*|--------------------------------------------------------------------------AUTH ROUTES--------------------------------------------------------------------------|*/

Route::get('/delete/{id}', function ($id) {
    $userId = $id;
    DB::transaction(function () use ($userId) {           
            DB::table('profiles')->where('user_id', $userId)->delete();
             DB::table('users')->where('id', $userId)->delete();
        });
    return response()->json(['message' => 'User and profile deleted successfully.']);
});