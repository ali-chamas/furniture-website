<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/signup', [AuthController::class, 'signup']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'delete']);
    Route::post('user/{id}/ban', [UserController::class, 'ban']);
    Route::get('/user', [UserController::class, 'getUser']);
});

Route::middleware([JwtMiddleware::class,AdminMiddleware::class])->group(function () {
    Route::get('/users', [UserController::class, 'getUsers']);
});
