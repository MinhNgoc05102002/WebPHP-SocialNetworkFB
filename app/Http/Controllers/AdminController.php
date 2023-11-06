<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Models\Account;
use App\Models\Report;
use Exception;
use Illuminate\Support\Facades\Validator;
use DB;
use DateTime;
use App\Events\NotificationEvent;
use App\Models\Notification;

class AdminController extends Controller
{
    protected $post;
    protected $account;
    protected $report;
    protected $notification;

    public function __construct(Post $_post, Account $_account, Report $_report, Notification $_notification) {
        $this->post = $_post;
        $this->account = $_account;
        $this->report = $_report;
        $this->notification=$_notification;
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

            // Thông tin số lượng tài khoản tạo mới trong 10 ngày gần nhất
            $numNewAccChart = $this->account->getNumNewAccountByDate();

            // Thông tin số lượng bài viết tạo mới trong 10 ngày gần nhất
            $numNewPostChart = $this->post->getNumNewPostByDate();

            // Thông tin số lượng report mới trong 10 ngày gần nhất
            $numNewReportChart = $this->report->getNumReportByDate();

            // Trả về response thành công
            return response()->success([
                "num_new_acc"=>$numNewAccount,
                "num_new_post"=>$numNewPost,
                "num_new_report"=>$numNewReport,
                "num_new_block"=>$numNewBlock,
                "list_age_range"=>$listAgeRange,
                "num_new_acc_chart"=>$numNewAccChart,
                "num_new_post_chart"=>$numNewPostChart,
                "num_new_report_chart"=>$numNewReportChart
            ],
                "Lấy dữ liệu thành công",
                200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getReportedPost(Request $request) {

        $validator = Validator::make($request->all(),
        [
         'page_size'=>'required|string',
         'page_index'=>'required|string',
        ]);

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pageSize = $request->input('page_size');
            $pageIndex = $request->input('page_index');
            $listReportedPost = $this->report->getListReportedPost($pageIndex, $pageSize);

            // Trả về response thành công
            return response()->success($listReportedPost,
                "Lấy dữ liệu thành công",
                200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getBlockedPost(Request $request) {

        $validator = Validator::make($request->all(),
        [
         'page_size'=>'required|string',
         'page_index'=>'required|string',
        ]);

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pageSize = $request->input('page_size');
            $pageIndex = $request->input('page_index');
            $listBlockedPost = $this->report->getListBlockedPost($pageIndex, $pageSize);

            // Trả về response thành công
            return response()->success($listBlockedPost,
                "Lấy dữ liệu thành công",
                200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getReportedAcc(Request $request) {
        $validator = Validator::make($request->all(),
        [
         'page_size'=>'required|string',
         'page_index'=>'required|string',
        ]);

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pageSize = $request->input('page_size');
            $pageIndex = $request->input('page_index');
            $listReportedAcc = $this->account->getListReportedAcc($pageIndex, $pageSize);

            // Trả về response thành công
            return response()->success($listReportedAcc,
                "Lấy dữ liệu thành công",
                200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handleBlockAcc(Request $request) {
        $validator = Validator::make($request->all(),
        [
         'blocked_username'=>'required|string',
        ]);

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $blockedUsername = $request->input('blocked_username');

            $newStatus = $this->account->handleBlockAcc($blockedUsername);

            // Trả về response thành công
            return response()->success($newStatus,
                "Lấy dữ liệu thành công",
                200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handleBlockPost(Request $request) {
        $validator = Validator::make($request->all(),
        [
         'blocked_post_id'=>'required',
        ]);

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $blockedPostId = $request->input('blocked_post_id');

            $newStatus = $this->post->handleBlockPost($blockedPostId);

            // Trả về response thành công
            return response()->success($newStatus,
                "Lấy dữ liệu thành công",
                200);

        } catch (Exception $e) {
            return response()->error("đã xảy ra lỗi", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendWarningAcc(Request $request) {
        $validator = Validator::make($request->all(),
        [
         'username'=>'required|string',
        ]);

        if ($validator->fails()) {
            // Xử lý khi validation thất bại, ví dụ trả về lỗi
            return response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $username = $request->input('username');

            // $this->account->sendWarningAcc($username); // status gửi tbao thành công hay thất bại

            date_default_timezone_set('Asia/Ho_Chi_Minh');

            $result = DB::select("call createNotiAdmin (:i_noti_type, :i_link, :i_sender_username, :i_username, :i_created_at);",[
                'i_noti_type' => 'ADMIN',
                'i_link' => '',
                'i_sender_username' => '',
                'i_username' => $username,
                'i_created_at' => date('Y-m-d H:i:s'),
            ]);
            DB::update("UPDATE Account SET has_warning = has_warning + 1 WHERE username = :username;",[
                'username' => $username,
            ]);

            $noti_id = $result[0]->noti_id;
            $data = $this->notification->getById($noti_id);

            $jsonStr = json_encode($data);
            event(new NotificationEvent($jsonStr,$data[0]->username));

            // Trả về response thành công
            return response()->success($result,
                "Gửi thông báo thành công",
                200);

        } catch (Exception $e) {
            return response()->error($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
