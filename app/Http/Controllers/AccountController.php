<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use DB;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Service\SendMail;

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

    public function uploadAvatar(Request $request){
        try {
            $username = auth()->user()->username;

            // up ảnh
            $imageInfo = array();
            if ($request->hasFile('media')) {
                $image = $request->file('media');
                $extension = $image->getClientOriginalExtension();
                $randomString = uniqid();
                $imageName = time()  . '' . $randomString . '.' . $extension;
                $image->move(public_path('storage/media'), $imageName);
                $imageInfo[] = ['type' => $image->getClientOriginalExtension(),'name' => $imageName];

                $result = DB::table('Account')
                    ->where('username', $username)
                    ->update([
                        'avatar' => $imageName,
                    ]);
                return response()->success($imageName,'Cập nhật avatar thành công',200);
                
            }
            return response()->error('Cập nhật avatar thất bại !',400);
         } catch (Throwable $th) {
             throw $th;
         }
    }

    public function uploadCoverBackground(Request $request){
        try {
            $username = auth()->user()->username;

            // up ảnh
            $imageInfo = array();
            if ($request->hasFile('media')) {
                $image = $request->file('media');
                $extension = $image->getClientOriginalExtension();
                $randomString = uniqid();
                $imageName = time() . '' . $randomString . '.' . $extension;
                $image->move(public_path('storage/media'), $imageName);
                $imageInfo[] = ['type' => $image->getClientOriginalExtension(),'name' => $imageName];

                $result = DB::table('Account')
                    ->where('username', $username)
                    ->update([
                        'cover' => $imageName,
                    ]);
                return response()->success($imageName,'Cập nhật cover background thành công',200);
                
            }
            return response()->error('Cập nhật cover background thất bại !',400);
         } catch (Throwable $th) {
             throw $th;
         }
    }

    public function resetPassword(Request $request) {
        $email = $request->input('email');
        $account = DB::table('Account')
            ->where('email', $email)
            ->first();
        if (!$account) {
            return response()->error('Không tìm thấy account nào có email này!',400);
        }
        $randomCode = bin2hex(random_bytes(4));
        $mailData = [
            'title' => 'Đặt lại mật khẩu cho tài khoản mạng xã hội',
            'body' => 'Bạn đang cố gắng đặt lại mật khẩu cho tài khoản mạng xã hội Facebook, tuyệt đối không chia sẻ mật khẩu này cho bất kì ai, với bất kì lý do gì. Mật khẩu mới của bạn là: ',
            'confirmationCode' => $randomCode,
            'email' => $email,
        ];
        try {
            DB::table('Account')
                    ->where('email', $email)
                    ->update([
                        'password' => $randomCode,
                    ]);
            Mail::to($request->email)->send(new SendMail($mailData));
            return response()->success([], 'Gửi mail đặt lại mật khẩu thành công!', 200);        }
        catch (Exception $e) {
            return response()->error('Gửi mail không thành công!',400);
        }
    }
}
//route -----
//Route::get('/accounts', [AccountController::class, 'getAll']);
