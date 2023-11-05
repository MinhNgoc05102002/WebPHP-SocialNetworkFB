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
use App\Events\MessageEvent;
use App\Events\ChatSessionEvent;

use DB;

class MessageController extends Controller
{
    /*
        Route::prefix('message')->group(function () {
        //KO CAN request - response: $chatsessions
        Route::get('/', [MessageController::class,'index']);
        //KO CAN request - response: $result{'messages' => $messages,'accounts' => $accounts}
        Route::get('chatsession/{chatId}', [MessageController::class, 'getChatSession']);
        //request: message, chat_id
        Route::post('addmessage', [MessageController::class, 'addMessage']);
        //request: chat_id, name
        Route::put('chatsession/changename', [MessageController::class, 'changeName']);
        //KO CAN request
        Route::delete('chatsession/delete/{chatId}', [MessageController::class, 'deleteChatSession']);
        //request: name
        Route::post('chatsession/create', [MessageController::class, 'createChatSession']);
        //
        Route::post('account', [MessageController::class, 'getChatSessionByUsername']);
    });
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

        $result = [];
        foreach ($chatSessions as $chatSession) {
            $chatId = $chatSession->chat_id;
            $account = $this->getChatPartner($chatId);
            $lastMessage = $this->getLastMessage($chatId); // Lấy tin nhắn mới nhất

            if ($account) {
                array_push($result, [
                        'chatSession' => $chatSession,
                        'account' => $account,
                        'message' => $lastMessage
                    ]
                );
            }
        }


        return response()->success($result, 'Lấy tất cả các đoạn chat thành công!', Response::HTTP_OK);
    }

    // Hàm để lấy tin nhắn mới nhất cho một đoạn chat session
    private function getLastMessage($chatId) {
        $query = "SELECT * FROM Message WHERE chat_id = :chatId ORDER BY created_at DESC LIMIT 1";
        $lastMessage = DB::select($query, ['chatId' => $chatId]);

        return $lastMessage ? $lastMessage[0] : null;
    }

    public function getChatSession($chatId)
    {
        $chatSession = ChatSession::where('chat_id', $chatId)->first();
        if (!$chatSession) {
            return response()->error('Không tìm thấy đoạn chat!', Response::HTTP_NOT_FOUND);
        }
        $messages = Message::where('chat_id', $chatId)->get();

        // Sử dụng Eloquent để lấy danh sách tài khoản thuộc chat session có chat_id = 1
        $accounts = $this->getChatPartner($chatId);
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
        }
        try{
            $message = new Message();
            $message->chat_id = $chatId;
            $message->message = $request->input('message');
            $message->username = $username;
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $message->created_at = date('Y-m-d H:i:s');
            $message->save();

            //gửi event
            $jsonStrMess = json_encode($message);
            $jsonStrSession = json_encode([
                "type" => "U",
                "chat_id" => $message->chat_id,
                "new_message" => $message->message
            ]);
            $partnerAccounts = $this->getChatPartner($chatId);

            foreach ($partnerAccounts as $partner){
                $nameChanel = $chatId.".".$partner->username;
                $nameChanelSession = $partner->username;

                event(new MessageEvent($jsonStrMess, $nameChanel));

                event(new ChatSessionEvent($jsonStrSession, $nameChanelSession));
            }

            return response()->success($message, 'Tin nhắn đã được gửi(thêm)!', 201);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getChatPartner($chatId){
        $currUsername = auth()->user()->username;

        // Lấy danh sách usernames thuộc chatsession
        $usernamesInChatSession = DB::table('AccountHasChatSession')
            ->select('username')
            ->where('chat_id', $chatId)
            ->where('username', '<>', $currUsername)
            ->pluck('username')
            ->toArray();

        if (empty($usernamesInChatSession)) {
            return []; // Hoặc bạn có thể xử lý lỗi tại đây
        }

        // Lấy thông tin tài khoản của đối tác
        $partnerAccounts = DB::table('Account')
            ->whereIn('username', $usernamesInChatSession)
            ->get();

        return $partnerAccounts;
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

    // public function addAccountToChat(Request $request){
    //     $chat_id = $request->input('chat_id');
    //     $chatSession = ChatSession::where('chat_id', $chat_id)->first();

    //     if (!$chatSession) {
    //         return response()->error('Không tìm thấy đoạn chat!', Response::HTTP_NOT_FOUND);
    //     }
    //     try{
    //         $AccountHasChat = new AccountHasChatSession();
    //         $AccountHasChat->username = $request->input('username');
    //         $AccountHasChat->chat_id = $chat_id;
    //         date_default_timezone_set('Asia/Ho_Chi_Minh');
    //         $AccountHasChat->time_send = date('Y-m-d H:i:s');
    //         $AccountHasChat->save();

    //         return response()->success([], 'Thêm người dùng vào đoạn chat thành công!', 200);
    //     }
    //     catch(Exception $ex){
    //         throw $ex;
    //     }
    // }

    public function getChatSessionByUsername(Request $request){
        $currUsername = auth()->user()->username;
        $partnerUsername = (string)$request->input('username');
        if (!$partnerUsername){
            return response()->error('Không tìm thấy gì!', Response::HTTP_NOT_FOUND);
        }

        try{
            $result = DB::select("call handleChatSession(:i_current_username, :i_partner_username);",[
                'i_current_username' => $currUsername,
                'i_partner_username' => $partnerUsername,
            ]);
            $avatar = '';
            $fullname = '';
            $avatar = $result[0]->avatar;
            $fullname = $result[0]->fullname;
            $jsonStrSession = json_encode([
                "type" => "C",
                "chat_id" => $result[0]->chat_id,
                "avatar" => $avatar,
                "fullname" => $fullname,
            ]);
            $nameChanelSession = $partnerUsername;
            event(new ChatSessionEvent($jsonStrSession, $nameChanelSession));
            // dd( $result);
            $chat_id = '';
            $chat_id = $result[0]->chat_id;
            return response()->success($chat_id, 'Lấy chat id thành công!', 200);
        }
        catch(Exception $ex){
            throw $ex;
        }
    }
}
