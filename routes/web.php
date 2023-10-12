<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MessageController;


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
    Route::get('/', [MessageController::class,'index']);
    Route::get('/getall', [MessageController::class,'getAll']);
    Route::post('/create', [MessageController::class,'create']);
    Route::get('chatsession/{chatId}', [MessageController::class, 'getMessagesByChatId']);
    Route::post('addmessage', [MessageController::class, 'addMessage']);
    Route::put('chatsession/{chatId}/changename', [MessageController::class, 'changeName']);
    Route::delete('deletemessages/chatsession/{chatId}', [MessageController::class, 'deleteMessagesByChatId']);
});