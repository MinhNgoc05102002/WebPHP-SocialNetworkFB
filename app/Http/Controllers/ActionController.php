<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ActionController extends Controller
{
    protected $account;
    protected $post;

    public function __construct(Account $_account,Post $_post )
    {
        $this->post=$_post;
        $this->account = $_account;
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

}
