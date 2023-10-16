<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;

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
    Route::get('/get-reported-post', [AdminController::class,'getReportedPost']);
    Route::get('/get-reported-acc', [AdminController::class,'getReportedAcc']);
});

Route::prefix('action')->group(function () {
    Route::get('/get-resultview', [ActionController::class,'getResultview']);
});

// private router
Route::middleware('auth:sanctum')->group(function () {

    /*
        index: lay ra tat ca cac chatsession

    */
    Route::prefix('message')->group(function () {
        //KO CAN request - response: $chatsessions
        Route::get('/', [MessageController::class,'index']);
        //KO CAN request - response: $result{'messages' => $messages,'accounts' => $accounts}
        Route::get('chatsession/{chatId}', [MessageController::class, 'getChatSession']);
        //request: message, chat_id
        Route::post('addmessage', [MessageController::class, 'addMessage']);
        //request: chat_id, name
        Route::put('chatsession/changename', [MessageController::class, 'changeName']);
        //KO CAN request
        Route::delete('chatsession/delete/{chatId}', [MessageController::class, 'deleteChatSession']);
        //request: chat_id, username
        Route::post('chatsession/addAccount', [MessageController::class, 'addAccountToChat']);
        //request: name
        Route::post('chatsession/create', [MessageController::class, 'createChatSession']);

    });

    Route::post('/logout', [AuthController::class,'logout']);

    Route::prefix('post')->group(function () {
        Route::get('/get-list', [PostController::class,'index']);



        Route::post('/handle-post', [PostController::class,'handlePost']);

    });
});

// public router
Route::get('/check-login', [AuthController::class,'checkLogin']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class,'register']);
Route::post('/upload-file', [AuthController::class,'uploadFile']);
Route::get('/media-file/{image}', [AuthController::class,'show']);





