<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Post;
use App\Models\React;
use App\Events\Message;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;

class ActionController extends Controller
{
    protected $account;
    protected $post;
    //
    protected $react;
    protected $notification;


    public function __construct(Account $_account,Post $_post ,  React $_react,  Notification $_notification)
    {
        $this->post=$_post;
        $this->account = $_account;
        $this->react=$_react;
        $this->notification=$_notification;

    }

    public function getResultview(Request $request)
    {
        try{

            $param = $request->query();
            $res_acc=$this->account->getResultAccount($param);
            $res_post=$this->post->getResultPost($param);
            return response()->success(['result_list_acc'=>$res_acc, 'result_list_post'=>$res_post],"Lấy dữ liệu thành công", 200);
        }catch(Exception $ex)
        {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function handleReact(Request $request)
    {
        // dd(auth()->user()->username);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // Các tham số đầu vào
        $i_react_type = $request->input('type');
        $i_post_id = $request->input('post_id');
        $i_username = auth()->user()->username;


            // Gọi thủ tục handleReact
            $result = DB::select("CALL handleReact(:i_react_type, :i_post_id, :i_username, :i_created_at)", [
                'i_react_type' => $i_react_type,
                'i_post_id' => $i_post_id,
                'i_username' => $i_username,
                'i_created_at' => date('Y-m-d H:i:s'),
            ]);
            if($result[0]->check_reacted==0){
                $noti_id = $result[0]->noti_id;
                $data = $this->notification->getById($noti_id);

                $jsonStr = json_encode($data);
                event(new Message($jsonStr,"tra-vh"));
            }


            // Trả về kết quả
            return response()->success($result,"Thực hiện thành công rồi!", 201);
    }

    public function createComment(Request $request)
    {
        // dd(auth()->user()->username);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // Các tham số đầu vào
        $i_content = $request->input('content');
        $i_post_id = $request->input('post_id');
        $i_username = auth()->user()->username;

            // Gọi thủ tục handleReact
            $result = DB::select("CALL createComment(:i_content, :i_post_id, :i_username, :i_created_at)", [
                'i_content' => $i_content,
                'i_post_id' => $i_post_id,
                'i_username' => $i_username,
                'i_created_at' => date('Y-m-d H:i:s'),
            ]);
            $noti_id = $result[0]->noti_id;
            $data = $this->notification->getById($noti_id);

            $jsonStr = json_encode($data);
            event(new Message($jsonStr));

            // Trả về kết quả
            return response()->success($result, "Tạo comment thành công!", 201);
    }

    public function updateComment(Request $request)
    {
        $i_comment_id = $request->input('comment_id');
        $i_content = $request->input('content');
        $i_post_id = $request->input('post_id');

        $result = DB::table('Comment')
            ->where('comment_id', $i_comment_id)
            ->update(['content' => $i_content]);

        // if ($result) {
        //     $message = "Sửa thành công";
        // } else {
        //     $message = "";
        // }

        // Trả về thông báo
        return response()->success($result, "Sửa comment thành công", 200);
        // Trả về kết quả

    }

    public function deleteComment(Request $request)
    {
        $comment_id = $request->input('comment_id');

        $result = DB::table('Comment')
            ->where('comment_id', $comment_id)
            ->delete();

        // Trả về kết quả
        // if ($result) {
        //     $message = "Xóa bình luận thành công";
        // } else {
        //     $message = "Không tìm thấy bình luận để xóa";
        // }

        // Trả về thông báo
        return response()->success($result,"Xóa comment thành công", 200);
    }

}

