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
Route::prefix('message')->group(function () {
    Route::get('/', [MessageController::class,'index']);
    Route::get('/getall', [MessageController::class,'getAll']);
    Route::post('/create', [MessageController::class,'create']);
    Route::get('chatsession/{chatId}', [MessageController::class, 'getMessagesByChatId']);
    Route::post('addmessage', [MessageController::class, 'addMessage']);
    Route::put('chatsession/{chatId}/changename', [MessageController::class, 'changeName']);
    Route::delete('deletemessages/chatsession/{chatId}', [MessageController::class, 'deleteMessagesByChatId']);
});