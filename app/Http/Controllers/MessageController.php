<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Message;
use App\Http\Requests\PostRequest;

use DB;

class MessageController extends Controller
{
    protected $post;
    public function __construct(Post $_post) {
        $this->post = $_post;
    }

    public function index(Request $request)
    {
        try{
            $page = $request->input('page', 1); // Số trang mặc định là 1.
            $perPage = $request->input('page_size', 10); // Số bản ghi mỗi trang mặc định là 10.
            $items = Post::paginate($perPage, ['*'], 'page', $page);
            return response()->success($items,"Lấy danh sách thành công", Response::HTTP_OK);
        }catch(Exception $ex){
            return response()->error("Lấy danh sách thất bại", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(PostRequest $request)
    {
       // Model binding và xác thực dữ liệu đã được thực hiện tự động
       $data = $request->validated();
       $new_post = $this->post->create($data);
       // Trả về response thành công
       return response()->success($new_post,"Tạo bài viết thành công !", 201);
    }

    public function getMessagesByChatId($chatId)
    {
        $chatSession = ChatSession::where('chat_id', $chatId)->first();
        if (!$chatSession) {
            return response()->json(['message' => 'ChatSession not found'], 404);
        }
        $messages = Message::where('chat_id', $chatId)->get();

        return response()->json(['data' => $messages]);
    }

    //thêm 1 message
    public function addMessage(Request $request, $chatId)
    {
        $chatSession = ChatSession::where('chat_id', $chatId)->first();

        if (!$chatSession) {
            return response()->json(['message' => 'ChatSession not found'], 404);
        }

        $message = new Message();
        $message->chat_id = $chatId;
        $message->message = $request->input('message');
        $message->save();

        return response()->json(['message' => 'Message added successfully']);
    }

    public function changeName(Request $request, $chatId)
    {
        $chatSession = ChatSession::where('chat_id', $chatId)->first();

        if (!$chatSession) {
            return response()->json(['message' => 'ChatSession not found'], 404);
        }

        $chatSession->name = $request->input('name');
        $chatSession->save();

        return response()->json(['message' => 'ChatSession name changed successfully']);
    }

    public function deleteMessagesByChatId($chatId)
    {
        Message::where('chat_id', $chatId)->delete();
        return response()->json(['message' => 'Messages deleted successfully']);
    }

    public function addAccountToChat(Request $request, $chatId){
        $chatSession = ChatSession::where('chat_id', $chatId)->first();

        if (!$chatSession) {
            return response()->json(['message' => 'ChatSession not found'], 404);
        }
        $AccountHasw = new Message();
        $message->chat_id = $chatId;
        $message->message = $request->input('message');
        $message->save();

        return response()->json(['message' => 'Message added successfully']);
    }
}

