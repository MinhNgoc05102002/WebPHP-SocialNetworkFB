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

        Route::post('/handle-post', [PostController::class,'handlePost']);

    });
});
Route::prefix('message')->group(function () {
    Route::get('/', [MessageController::class,'index']);
    Route::get('/getall', [MessageController::class,'getAll']);
    Route::post('/create', [MessageController::class,'create']);
    Route::get('chatsession/{chatId}', [MessageController::class, 'getMessagesByChatId']);
    Route::post('addmessage', [MessageController::class, 'addMessage']);
    Route::put('chatsession/{chatId}/changename', [MessageController::class, 'changeName']);
    Route::delete('deletemessages/chatsession/{chatId}', [MessageController::class, 'deleteMessagesByChatId']);
});

// public router
Route::get('/check-login', [AuthController::class,'checkLogin']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class,'register']);
Route::post('/upload-file', [AuthController::class,'uploadFile']);
Route::get('/media-file/{image}', [AuthController::class,'show']);





