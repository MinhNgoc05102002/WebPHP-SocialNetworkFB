<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\AuthController;

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







Route::prefix('admin')->group(function () {
    Route::get('/get-overview', [AdminController::class,'getOverview']);

});

Route::prefix('action')->group(function () {
    Route::get('/get-resultview', [ActionController::class,'getResultview']);
});

// private router
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);

    Route::prefix('post')->group(function () {
        Route::get('/get-list', [PostController::class,'index']);

        Route::post('/create', [PostController::class,'create']);
    });
});

// public router
Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class,'register']);





