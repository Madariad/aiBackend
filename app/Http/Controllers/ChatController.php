<?php

namespace App\Http\Controllers;


use App\Models\Chat;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\conversationHistory;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function createChat(Request $request)
    {
        $chat = new Chat();
        $chat->user_id = $request->user()->id;
        $chat->name = "New Chat #". time() % 1000000;
        $chat->chat_code = Str::uuid();
        $chat->save();

       
        return response()->json([
            'message' => 'success'
        ], 201);
    }
    public function getChats(Request $request)
    {
        $chats = Chat::where('user_id', $request->user()->id)->get();
        return response()->json([
           'message' => 'success',
            'chats' => $chats,
        ], 200);
    }

    public function getChat(Request $request){
        $chat_code = $request->header('chat_code') ; 
      
    
        if ($chat_code) {
            $chat = Chat::where('user_id', $request->user()->id)
                        ->where('chat_code', $chat_code)
                        ->first();
            $conversationHistory = conversationHistory::where('chat_id', $chat->id)->get();
    
            if ($chat) {
                return response()->json([
                    'message' => 'success',
                    'chat' => $chat,
                    'conversationHistory' => $conversationHistory,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Chat not found',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Invalid request',
            ], 400);
        }
    }


}
