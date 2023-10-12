<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Message;
use App\Http\Requests\PostRequest;
use App\Models\ChatSession;

use DB;

class MessageController extends Controller
{

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

