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
        try{
            $page = $request->input('page', 1); // Số trang mặc định là 1.
            $perPage = $request->input('page_size', 50); // Số bản ghi mỗi trang mặc định là 10.
            $items = Post::paginate($perPage, ['*'], 'page', $page);
            return response()->success($items,"Lấy danh sách thành công", Response::HTTP_OK);
        }catch(Exception $ex){
            return response()->error($ex, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handlePost(PostRequest $request)
    {
       try{
            $validate = $request->validated();
            $account = $this->account->getInfoAccount($request->input('username'));
            if($account){
                $func = $request->input('function');
                if($func === 'C'){
                    // tạo bài viết
                    $newPost = $this->post->createPost($request->only('username','content','audience_type','media'));
                    
                    return response()->success($newPost,"Tạo bài viết thành công !", 201);
                }elseif($func === 'U'){
                    // cập nhật bài viết
                    $postFind = Post::find($request->input('id_post'));
                    if($postFind){
                        $updatedPost = $this->post->updatePost(
                            $request->only('username','content','audience_type','media','id_post'),
                            $postFind
                        );
                    }else{
                        return response()->error("Bài viết không tồn tại !", 401);
                    }
    
                    return response()->success($updatedPost,"Cập nhật bài viết thành công !", 201);
                }elseif($func === 'D'){
                    // Xóa bài viết
                    $postFind = Post::find($request->input('id_post'));
                    if($postFind){
                        $updatedPost = $this->post->deletePost(
                            $request->only('id_post'),
                            $postFind
                        );
                    }else{
                        return response()->error("Bài viết không tồn tại !", 401);
                    }
    
                    return response()->success($updatedPost,"Xóa bài viết thành công !", 201);
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
}
