<?php

use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any("welcome", function () {
    $message = \Illuminate\Foundation\Inspiring::quote();
    return "<h3> $message</h3>";
});
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {
        Route::get('', function (Request $request) {
            return $request->user();
        });
        Route::post('register', [UserController::class, 'register']);
        Route::post('update/{user}', [UserController::class, 'update']);
        Route::post('delete/{user}', [UserController::class, 'destroy']);
    });
    Route::get('requests', [RequestController::class,'index']);
    Route::post('requests/approve/{ref}', [RequestController::class,'approve']);
});

