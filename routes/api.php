<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

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


Route::prefix('post')->group(function () {
    Route::get('/get-list', [PostController::class,'index']);

    Route::post('/create', [PostController::class,'create']);

    // Route::get('/getFilter', [PostController::class,'getFilter']);

    // Route::get('/getFilter', [PostController::class,'getFilter']);
});
