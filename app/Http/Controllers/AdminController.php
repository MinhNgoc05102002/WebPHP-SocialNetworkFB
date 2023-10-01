<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Account;
use App\Models\Report;
use App\Http\Controllers\Exception;

class AdminController extends Controller
{
    protected $post;
    protected $account;
    protected $report;

    public function __construct(Post $_post, Account $_account, Report $_report) {
        $this->post = $_post;
        $this->account = $_account;
        $this->report = $_report;
    }

    public function getOverview(Request $request) {
        try {
            // Lấy số lượng tài khoản tạo mới trong 7 ngày gần nhất DucTM
            $numNewAccount = $this->account->getNumRecentAccount();

            // Lấy số lượng bài viết mới trong 7 ngày gần nhất SyLV
            $numNewPost = $this->post->getNumRecentPost();

            // Trả về response thành công
        return response()->success([$numNewAccount, $numNewPost],"Lấy dữ liệu thành công", 200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
