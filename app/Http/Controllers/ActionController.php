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

    //tạo like
    public function createReact(Request $request)
    {
        //dd($request->input('username'),$request->input('type'),$request->input('post_id'));
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // Các tham số đầu vào
        $i_react_type = $request->input('type');
        $i_post_id = $request->input('post_id');
        $i_username = $request->input('username');


            // Gọi thủ tục createReact
            $result = DB::select("CALL createReact(:i_react_type, :i_post_id, :i_username, :i_created_at)", [
                'i_react_type' => $i_react_type,
                'i_post_id' => $i_post_id,
                'i_username' => $i_username,
                'i_created_at' => date('Y-m-d H:i:s'),
            ]);
            if($result[0]->check_reacted==0){
                $noti_id = $result[0]->noti_id;
                $data = $this->notification->getById($noti_id);

                $jsonStr = json_encode($data);
                event(new Message($jsonStr));
            }
            // Trả về kết quả
            return response()->success($result,"Thực hiện thành công rồi!", 201);
    }


    //xóa like
    public function deleteReact(Request $request)
    {
        //dd($request->input('post_id'),$request->input('username'));
        $i_post_id = $request->input('post_id');
        $i_username = $request->input('username');

            // Gọi thủ tục deleteReact
            $result = DB::select("CALL deleteReact(:i_post_id, :i_username)", [
                'i_post_id' => $i_post_id,
                'i_username' => $i_username,
            ]);
            // Trả về kết quả
            return response()->success($result,"Thực hiện thành công rồi!", 200);
    }

    //tạo comment
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

    //sửa comment
    public function updateComment(Request $request)
    {
        $i_comment_id = $request->input('comment_id');
        $i_content = $request->input('content');
        $i_post_id = $request->input('post_id');

        $result = DB::table('Comment')
            ->where('comment_id', $i_comment_id)
            ->update(['content' => $i_content]);



        // Trả về thông báo
        return response()->success($result, "Sửa comment thành công", 200);
        // Trả về kết quả

    }

    //xoá comment
    public function deleteComment(Request $request)
    {
        $comment_id = $request->input('comment_id');

        $result = DB::table('Comment')
            ->where('comment_id', $comment_id)
            ->delete();



        // Trả về thông báo
        return response()->success($result,"Xóa comment thành công", 200);
    }

    //lấy profile
    public function getProfile(Request $request)
    {
        //dd(auth()->user()->username,$request->input('username'));
        // Các tham số đầu vào
        $i_current_username = auth()->user()->username;
        $i_profile_username = $request->input('username');

            // Gọi thủ tục handleReact
            $result = DB::select("CALL getProfile(:i_current_username, :i_profile_username)", [
                'i_current_username' => $i_current_username,
                'i_profile_username' => $i_profile_username
            ]);
        return response()->success($result,"Lấy profile thành công", 200);
    }
    //kết bạn, hủy kết bạn
    public function handleRelationship(Request $request)
    {
        dd(auth()->user()->username,$request->input('target_username'));
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // Các tham số đầu vào
        $i_action = $request->input('action');
        $i_source_username = auth()->user()->username;
        $i_target_username = $request->input('target_username');


            // Gọi thủ tục handleReact
            $result = DB::select("CALL handleRelationship(:i_action, :i_source_username, :i_target_username, :i_created_at)", [
                'i_action' => $i_action,
                'i_source_username' => $i_source_username,
                'i_target_username' => $i_target_username,
                'i_created_at' => date('Y-m-d H:i:s'),
            ]);
        return response()->success($result,"Thực hiện thành công", 200);
    }
}

