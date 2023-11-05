<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Notification;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use App\Events\Message;
use DB;
use Exception;

class NotifiController extends Controller
{
    protected $notification;
    public function __construct(Notification $notification) {
        $this->notification = $notification;
    }

    public function index(Request $request)
    {
        $username = auth()->user()->username;
        if(!$username){
            return response()->error("Người dùng không hợp lệ", 404);
        }
        try {
            $query = "SELECT n.*,a.fullname,c.value as content_noti,a.avatar FROM Notification n
                      LEFT JOIN Account a ON n.sender_username = a.username
                      INNER JOIN Classification c ON c.code = n.noti_type
                      WHERE n.username = :username
                      ORDER BY n.created_at DESC";
            $lstNoti = DB::select($query, ['username' => $username]);

            return response()->success($lstNoti,"Lấy danh sách thành công", Response::HTTP_OK);
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function demoNotification(Request $request){
        $data = [
            'message' => 'xin chao',
            'username' => 'duc'
        ];
        $jsonStr = json_encode($data);
        event(new Message($jsonStr));
        return response()->success([],"Bài viết không tồn tại !", 401);
    }
}
