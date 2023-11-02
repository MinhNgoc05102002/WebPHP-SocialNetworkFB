<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Models\Account;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use App\Events\Message;
use App\Events\YourEventName;
use DB;
use Exception;

class PostController extends Controller
{
    protected $post;
    protected $account;   
    public function __construct(Post $_post,Account $account) {
        $this->post = $_post;
        $this->account = $account;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
         'page_count'=>'required|string',
         'page_index'=>'required|string',
        ]
        );

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        

        try{
            $username = auth()->user()->username; // lấy username trong phiên đăng nhập
            $pageCount = $request->input('page_count');
            $pageIndex = $request->input('page_index');
            $lstPost = $this->post->getListPostByFilter($pageCount,$pageIndex,$username);
            return response()->success($lstPost,"Lấy danh sách thành công", Response::HTTP_OK);
        }catch(Exception $ex){
            throw $ex;
        }
    }

    public function getListPostProfile(Request $request){
        $validator = Validator::make($request->all(),
        [
         'page_count'=>'required|string',
         'page_index'=>'required|string',
        ]
        );

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $username = auth()->user()->username; // lấy username trong phiên đăng nhập
            $pageCount = $request->input('page_count');
            $pageIndex = $request->input('page_index');
            $usernameProfile = $request->input('profile_username');
            $lstPost = $this->post->getListPostProfile($pageCount,$pageIndex,$username,$usernameProfile);
            return response()->success($lstPost,"Lấy danh sách thành công", Response::HTTP_OK);
        }catch(Exception $ex){
            throw $ex;
        }
    }

    

    public function handlePost(PostRequest $request)
    {
       try{
            $validate = $request->validated();
            $username = auth()->user()->username;
            $account = $this->account->getInfoAccount($username);
            $media = null;
            if($request->has('media')){
                $media = $request->input('media');
            }
            if($account){
                $func = $request->input('function');
                if($func === 'C'){
                    // tạo bài viết
                    $newPost = $this->post->createPost($request->only('content','audience_type'),$username,$media);
                    
                    return response()->success($newPost,"Tạo bài viết thành công !", 201);
                }elseif($func === 'U'){
                    // cập nhật bài viết
                    $postFind = Post::find($request->input('id_post'));
                    if($postFind){
                        $updatedPost = $this->post->updatePost(
                            $request->only('content','audience_type','id_post'),
                            $postFind,
                            $username,
                            $media
                        );
                    }else{
                        return response()->error("Bài viết không tồn tại !", 401);
                    }
                    if($updatedPost){
                        return response()->success($updatedPost,"Cập nhật bài viết thành công !", 201);
                    }else{
                        return response()->error("Cập nhật bài viết thất bại", 401);
                    }
    
                }elseif($func === 'D'){
                    // Xóa bài viết
                    $postFind = Post::find($request->input('id_post'));
                    if($postFind){
                        $updatedPost = $this->post->deletePost(
                            $request->only('id_post'),
                            $postFind,
                            $username
                        );
                        if($updatedPost){
                            return response()->success($request->input('id_post'),"Xóa bài viết thành công !", 201);
                        }else{
                            return response()->error("Xóa bài viết thất bại !", 301);
                        }
                    }else{
                        return response()->error("Bài viết không tồn tại !", 401);
                    }

                    
                }else{
                    return response()->error("Chức năng không tồn tại !", 401);
                }

            }

            return response()->error("Tài khoản không hợp lệ", 401);
            // Trả về response thành công
       }catch(Exception $ex){
            throw $ex;
       }
    }

    public function getPostById(Request $request){
            $validator = Validator::make($request->all(),
            [
                'post_id'=>'required|string',
            ]
            );
            if ($validator->fails()) {
                // Xử lý khi validation thất bại, ví dụ trả về lỗi
                return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            try {
            $username = auth()->user()->username;
            $post = $this->post->getPostById($request->input("post_id"),$username);
            if($post){
                return response()->success($post,"Lấy chi tiết bài viết thành công", Response::HTTP_OK);
            }else{
                return response()->error("Không tồn tại post", Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Throwable $th) {
            dd($th);
            return response()->error($th, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

    public function demoNotification(Request $request){
        $data = [
            'message' => 'xin chao',
            'username' => 'duc'
        ];
        $jsonStr = json_encode($data);
        event(new Message($jsonStr,'tra-vh'));
        return response()->success([],"Bài viết không tồn tại !", 401);
    }
}
