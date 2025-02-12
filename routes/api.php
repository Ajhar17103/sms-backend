<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MenuController;


Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class,'login']);
    Route::post('register', [AuthController::class,'register']);
});

Route::middleware(['auth:api'])->group(function(){
    // Auth Routes
    // Route::post('register', [AuthController::class,'register']);
    Route::post('user-details', [AuthController::class,'userDetails']);
    Route::post('refresh-token', [AuthController::class,'refresh']);
    Route::post('logout', [AuthController::class,'logout']);

    // Roles Routes
    Route::get('/role/all', [RoleController::class, 'index']);
    Route::post('/role/create', [RoleController::class, 'store']);
    Route::get('/role/details/{id}', [RoleController::class, 'show']);
    Route::put('/role/update/{id}', [RoleController::class, 'update']);
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);

    // Menu Routes
    Route::get('/menu/all', [MenuController::class, 'index']);
    Route::post('/menu/create', [MenuController::class, 'store']);
    Route::get('/menu/details/{id}', [MenuController::class, 'show']);
    Route::put('/menu/update/{id}', [MenuController::class, 'update']);
    Route::delete('/menu/delete/{id}', [MenuController::class, 'destroy']);
});
