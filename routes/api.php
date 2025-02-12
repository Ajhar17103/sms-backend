<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;


// routes/api.php
Route::get('/test', function () {
    return response()->json(['message' => 'Test route is working']);
});

Route::group(['prefix' => 'auth'], function ($router) {
	Route::post('/login', [AuthController::class, 'login']);
	Route::post('/register', [AuthController::class, 'register']);

});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [AuthController::class, 'users']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
});

