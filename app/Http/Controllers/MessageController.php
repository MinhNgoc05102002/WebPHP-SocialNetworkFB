<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Message;
use App\Http\Requests\PostRequest;
use App\Models\ChatSession;
use App\Models\AccountHasChatSession;
use App\Models\Account;

use DB;

class MessageController extends Controller
{
    /*
        1: tra ve sucess thay vi json
        vd: return response()->success($items,"Lấy danh sách thành công", Response::HTTP_OK);
        (object, "comment", status_code vd 200 = success)
    */
    public function index() {
        $username = auth()->user()->username;
        $query = "SELECT cs.chat_id, cs.name
                FROM ChatSession cs
                INNER JOIN AccountHasChatSession acs ON cs.chat_id = acs.chat_id
                INNER JOIN Account a ON acs.username = a.username
                WHERE a.username = :username";

        $chatSessions = DB::select($query, ['username' => $username]);
        if (!$chatSessions) {
            return response()->success([], 'Người dùng ' . $username . ' không ở trong đoạn chat nào!', Response::HTTP_OK);
        }
        return response()->success($chatSessions, 'Lấy tất cả các đoạn chat thành công!', Response::HTTP_OK);
    }

    public function getChatSession($chatId)
    {
        $chatSession = ChatSession::where('chat_id', $chatId)->first();
        if (!$chatSession) {
            return response()->error('Không tìm thấy đoạn chat!', Response::HTTP_NOT_FOUND);
        }
        $messages = Message::where('chat_id', $chatId)->get();

        // Sử dụng Eloquent để lấy danh sách tài khoản thuộc chat session có chat_id = 1
        $accounts = Account::join('AccountHasChatSession', 'Account.username', '=', 'AccountHasChatSession.username')
            ->where('AccountHasChatSession.chat_id', $chatId)
            ->get();
        $result = [
            'messages' => $messages,
            'accounts' => $accounts
        ];
        return response()->success($result, 'Lấy tin nhắn và các tài khoản trong đoạn chat thành công!', Response::HTTP_OK);
    }

    //thêm 1 message
    public function addMessage(Request $request)
    {
        $username = auth()->user()->username;
        $chatId = $request->input('chat_id');
        $chatSession = ChatSession::where('chat_id', $chatId)->first();

        if (!$chatSession) {
            return response()->error('Không tìm thấy đoạn chat để thêm tin nhắn!', Response::HTTP_NOT_FOUND);
            //return response()->json(['message' => 'ChatSession not found'], 404);
        }
        try{
            $message = new Message();
            $message->chat_id = $chatId;
            $message->message = $request->input('message');
            $message->username = $username;
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $message->created_at = date('Y-m-d H:i:s');
            $message->save();

            return response()->success($message, 'Tin nhắn đã được gửi(thêm)!', 201);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function changeName(Request $request)
    {
        $chat_id = $request->input('chat_id');
        $chatSession = ChatSession::where('chat_id', $chat_id)->first();

        if (!$chatSession) {
            return response()->error('Đổi tên thất bại, không tìm thấy đoạn chat!', Response::HTTP_NOT_FOUND);
            // return response()->json(['message' => 'ChatSession not found'], 404);
        }

        try{
            $chatSession->name = $request->input('name');
            $chatSession->save();

            return response()->success($chatSession, "Đổi tên thành công!", 200);
        }
        catch(Exception $ex){
            throw $ex;
        }
    }

    public function deleteChatSession($chatId)
    {
        ChatSession::where('chat_id', $chatId)->delete();
        Message::where('chat_id', $chatId)->delete();
        AccountHasChatSession::where('chat_id', $chatId)->delete();
        //return response()->json(['message' => 'Messages deleted successfully']);
        return response()->success([], 'Xóa đoạn chat thành công!', 200);
    }

    public function addAccountToChat(Request $request){
        $chat_id = $request->input('chat_id');
        $chatSession = ChatSession::where('chat_id', $chat_id)->first();

        if (!$chatSession) {
            return response()->error('Không tìm thấy đoạn chat!', Response::HTTP_NOT_FOUND);
        }
        try{
            $AccountHasChat = new AccountHasChatSession();
            $AccountHasChat->username = $request->input('username');
            $AccountHasChat->chat_id = $chat_id;
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $AccountHasChat->time_send = date('Y-m-d H:i:s');
            $AccountHasChat->save();

            return response()->success([], 'Thêm người dùng vào đoạn chat thành công!', 200);
        }
        catch(Exception $ex){
            throw $ex;
        }
    }

    public function createChatSession(Request $request){
        try{
            $chatSession = new ChatSession();
            $chatSession->name = $request->input('name');
            $chatSession->save();

            return response()->success([], 'Tạo đoạn chat thành công!', 200);
        }
        catch(Exception $ex){
            throw $ex;
        }
    }
    
    // public function search(Request $request){
    // }

    // public function 

}