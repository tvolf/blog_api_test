<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::post('register', [ RegisterController::class, 'register'])->name('auth.register')->middleware('guest');
Route::post('login', [ LoginController::class, 'login'])->name('auth.login')->middleware('guest');

Route::apiResource('posts', PostController::class)->only(['index', 'show']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('posts', PostController::class)->only(['store', 'update', 'destroy']);
});


//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
