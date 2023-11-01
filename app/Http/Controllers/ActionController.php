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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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
        $i_username = auth()->user()->username;
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $i_username = auth()->user()->username;
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'commnet_id' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
        $validator = Validator::make($request->all(), [
            'commnet_id' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
        $i_profile_username = $request->input('profile_username');
        $validator = Validator::make($request->all(), [
            'profile_username' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        //dd(auth()->user()->username,$request->input('target_username'));
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // Các tham số đầu vào
        $i_action = $request->input('action');
        $i_source_username = auth()->user()->username;
        $i_target_username = $request->input('target_username');
        $validator = Validator::make($request->all(), [
            'action' => 'required',
            'target_username' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

            // Gọi thủ tục handleReact
            $result = DB::select("CALL handleRelationship(:i_action, :i_source_username, :i_target_username, :i_created_at)", [
                'i_action' => $i_action,
                'i_source_username' => $i_source_username,
                'i_target_username' => $i_target_username,
                'i_created_at' => date('Y-m-d H:i:s'),
            ]);
        return response()->success($result,"Thực hiện thành công", 200);
    }

    //tìm kiếm tài khoản và bài viết
    public function searchAccountsAndPosts(Request $request)
    {
        $searchTerm = $request->input('search_term');
        $validator = Validator::make($request->all(), [
            'search_term' => 'required',
        ]);

        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        try {
            // Tìm kiếm danh sách tài khoản và bài viết
            $matchedAccounts = Account::where('fullname', 'LIKE', '%' . $searchTerm . '%')
                            ->select('fullname', 'avatar', 'number_friend')
                            ->paginate(10);
                            //->get();
            $matchedPosts = Post::where('content', 'LIKE', '%' . $searchTerm . '%')
                            ->join('Account', 'Post.username', '=', 'Account.username')
                            ->select('Post.*', 'Account.avatar', 'Account.fullname')
                            ->paginate(10);
                            //->get();
            // Xử lý kết quả và trả về response
            return response()->success(['matched_accounts' => $matchedAccounts, 'matched_posts' => $matchedPosts],"Lấy dữ liệu thành công", 200);
        } catch (\Exception $e) {
            // Xử lý lỗi chung
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Cập nhật profile
    public function updateProfile(Request $request)
    {
        try{
            $username = auth()->user()->username;
            $avatar = $request->input('avatar');
            $fullname = $request->input('fullname');
            $location = $request->input('location');
            $phone = $request->input('phone');
            $aboutme = $request->input('about_me');

            // Tạo validator để kiểm tra các trường
            $validator = Validator::make($request->all(), [
                'fullname' => 'nullable|regex:/^[\p{L}\p{M}\p{Pd}\p{Zs}\']+$/u',
                'location' => 'nullable|address',
                'phone' => 'nullable|phone',
                'about_me' => 'nullable',
            ]);

            // Kiểm tra validator
            if ($validator->fails()) {
                //
                response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Cập nhật thông tin cá nhân trong cơ sở dữ liệu
            $result = DB::table('Account')
                    ->where('username', $username)
                    ->update([
                        'avatar' => $avatar,
                        'fullname' => $fullname,
                        'location' => $location,
                        'phone' => $phone,
                        'about_me' => $aboutme
                    ]);
            //dd($username,$avatar, $fullname,$location,$phone, $aboutme);
            // Trả về thông báo thành công
            return response()->success($result, 'Cập nhật thông tin cá nhân thành công', 200);

        }catch(Exception $ex){
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    //chức năng đổi mật khẩu
    public function changePassword(Request $request)
    {
        $username = auth()->user()->username;
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');

        // Tạo validator để kiểm tra các trường
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        // Kiểm tra validator
        if ($validator->fails()) {
            //
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $result = DB::table('Account')
            ->where('username', $username)
            ->first();

        if (!$result) {
            response()->error("người dùng không tồn tại", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($currentPassword, $result->password)) {
            response()->error("Mật khẩu hiện tại không đúng", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Mã hóa mật khẩu mới
        $hashedPassword = Hash::make($newPassword);

        // Tiếp tục câu truy vấn update
        $result = DB::table('Account')
                    ->where('username', $username)
                    ->update([
                        'password' => $hashedPassword,
                // Cập nhật các trường khác tại đây (nếu có)
            ]);

        // Kiểm tra kết quả của câu truy vấn update
        if ($result) {
            return response()->success($result, 'Cập nhật mật khẩu thành công', 200);
        } else {
            response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

