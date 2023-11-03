<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    //
    public function getAll()
    {
        $items = Account::all();
        return response()->json($items);
    }
    public function getItem($id)
    {
        $item = Account::find($id);
        return response()->json($item);
    }

    public function create(Request $request)
    {
        $item = new Account;
        $item->username = $request->input('username');
        $item->email = $request->input('email');
        $item->password = $request->input('password');
        $item->name = $request->input('name');
        $item->aboutMe = $request->input('aboutMe');
        $item->email = $request->input('location');
        $item->email = $request->input('numberNoti');
        $item->email = $request->input('phone');
        $item->email = $request->input('numberFriend');
        $item->isActive = true;
        $item->avatar = $request->input('avatar');
        $item->save();

        return response()->json($item);
    }

    public function deleteOne($id)
    {
        $item = Account::find($id);
        $item->delete();

        return response()->json(['message' => 'Acc deleted']);
    }
}
//route -----
//Route::get('/accounts', [AccountController::class, 'getAll']);
