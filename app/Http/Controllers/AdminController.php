<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Models\Account;
use App\Models\Report;
use Exception;

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
            // Lấy số lượng tài khoản tạo mới trong 7 ngày gần nhất
            $numNewAccount = $this->account->getNumNewAccount();

            // Lấy số lượng bài viết mới trong 7 ngày gần nhất
            $numNewPost = $this->post->getNumNewPost();

            // Số lượng lượt report mới trong 7 ngày gần nhất
            $numNewReport = $this->report->getNumNewReport();

            // Số lượng account bị khóa trong 7 ngày gần nhất
            $numNewBlock = $this->account->getNumNewBlock();

            // Thông tin tỉ lệ độ tuổi của ng dùng
            $listAgeRange = $this->account->getNumAccByAge();

            // Thông tin số lượt đki mới theo từng ngày trong 7 ngày qua
            // Thông tin lượng bài viết mới theo từng ngày trong 7 ngày qua
            // Thông tin số lượt report mới theo từng ngày trong 7 ngày qua
            // Trả về response thành công
            return response()->success([
                "numNewAccount"=>$numNewAccount,
                $numNewPost,
                $numNewReport,
                $numNewBlock,
                "listAgeRange"=>$listAgeRange
            ],
                "Lấy dữ liệu thành công",
                200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
