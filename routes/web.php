<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use routes\api_messages;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/account', [AccountController::class,'getAll']);
//route
Route::prefix('message')->group(function () {
    Route::get('/getall', [MessageController::class,'index']);
    Route::post('/create', [MessageController::class,'create']);
    Route::get('chatsession/{chatId}', [ChatSessionController::class, 'getMessagesByChatId']);
    Route::post('addmessage', [ChatSessionController::class, 'addMessage']);
    Route::put('chatsession/{chatId}/changename', [ChatSessionController::class, 'changeName']);
    Route::delete('deletemessages/chatsession/{chatId}', [ChatSessionController::class, 'deleteMessagesByChatId']);
});