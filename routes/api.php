<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TokenController;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => '/v1'], function () {
    Route::get('/token', [TokenController::class, 'generateTokenForRegistration']);
    Route::post('/users', [AuthController::class, 'register']);
    Route::get('/users', [UserController::class, 'usersList'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'getUserById'])->name('users.index');
    Route::get('/positions', [UserController::class, 'getUserPositions'])->name('users.index');
});