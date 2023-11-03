<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotifiController;
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
    Route::post('/get-reported-post', [AdminController::class,'getReportedPost']);
    Route::post('/get-reported-acc', [AdminController::class,'getReportedAcc']);
    Route::post('/handle-block-acc', [AdminController::class,'handleBlockAcc']);
    Route::post('/handle-block-post', [AdminController::class,'handleBlockPost']);
    Route::post('/send-warning-acc', [AdminController::class,'sendWarningAcc']);
    Route::post('/get-blocked-post', [AdminController::class,'getBlockedPost']);
});



// private router
Route::middleware('auth:sanctum')->group(function () {

    /*
        index: lay ra tat ca cac chatsession

    */
    Route::prefix('message')->group(function () {
        Route::get('/', [MessageController::class,'index']);
        Route::get('/get-chatsession/{chatId}', [MessageController::class, 'getChatSession']);
        Route::post('/add-message', [MessageController::class, 'addMessage']);
        Route::post('/get-chat-id-by-username', [MessageController::class, 'getChatSessionByUsername']);

        Route::post('/changename', [MessageController::class, 'changeName']);
        Route::post('/delete-chatsesion/{chatId}', [MessageController::class, 'deleteChatSession']);
        Route::post('/create-chatsession', [MessageController::class, 'createChatSession']);
    });

    Route::post('/logout', [AuthController::class,'logout']);

    Route::prefix('post')->group(function () {
        Route::get('/get-list', [PostController::class,'index']);

        Route::post('/handle-post', [PostController::class,'handlePost']);

        Route::get('/get-list-profile', [PostController::class,'getListPostProfile']);

        Route::post('/get-post-by-id', [PostController::class,'getPostById']);

    });

    Route::prefix('action')->group(function () {
        Route::get('/get-resultview', [ActionController::class,'getResultview']);
        Route::post('/create-react', [ActionController::class,'createReact']);
        Route::post('/delete-react', [ActionController::class,'deleteReact']);
        Route::post('/list-comment', [ActionController::class,'getListComment']);
        Route::post('/create-comment', [ActionController::class,'createComment']);
        Route::post('/update-comment', [ActionController::class,'updateComment']);
        Route::post('/delete-comment', [ActionController::class,'deleteComment']);
        Route::post('/get-profile', [ActionController::class,'getProfile']);
        Route::post('/handle-relationship', [ActionController::class,'handleRelationship']);
        Route::post('/search-accounts-posts', [ActionController::class,'searchAccountsAndPosts']);
        Route::post('/update-profile', [ActionController::class,'updateProfile']);
        Route::post('/change-password', [ActionController::class,'changePassword']);
		Route::post('/change-avatar', [AccountController::class,'uploadAvatar']);
		Route::post('/change-cover-bg', [AccountController::class,'uploadCoverBackground']);
		Route::post('/get-list-account', [ActionController::class,'getRequest']);
    });

    Route::prefix('notification')->group(function () {
        Route::get('/', [NotifiController::class,'index']);
    });
});

// public router
Route::get('/check-login', [AuthController::class,'checkLogin']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class,'register']);
Route::post('/upload-file', [AuthController::class,'uploadFile']);
Route::get('/media-file/{image}', [AuthController::class,'show']);
Route::get('/notifi-realtime', [PostController::class,'demoNotification']);
Route::post('/reset-password', [AccountController::class,'resetPassword']);