<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Models\Account;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;


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
            $perPage = $request->input('page_size', 10); // Số bản ghi mỗi trang mặc định là 10.
            $items = Post::paginate($perPage, ['*'], 'page', $page);
            return response()->success($items,"Lấy danh sách thành công", Response::HTTP_OK);
        }catch(Exception $ex){
            return response()->error($ex, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(PostRequest $request)
    {
       try{
            $validate = $request->validated();
            $account = $this->account->getInfoAccount($request->input('username'));
            if($account){
                $newPost = $this->post->create($request->only('username','content','audience_type'));

                return response()->success($newPost,"Tạo bài viết thành công !", 201);
            }

            return response()->error("Tài khoản không hợp lệ", 401);
            // Trả về response thành công
       }catch(Exception $ex){
            throw $ex;
       }
    }
}
