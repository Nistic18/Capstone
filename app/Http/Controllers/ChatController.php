<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function index(Request $request)
    {
        return view('chat');
    }
    
    public function getChatList(Request $request)
    {
        $messages = User::leftJoin('messages as m', 'users.id', '=', 'm.user_id')
    ->where('users.id', '!=', 1)
    ->orderBy('m.created_at', 'desc')
    ->select(
        'users.id as user_id',
        'users.name',
        'users.email',
        'users.email_verified_at',
        'users.role',
        'users.created_at',
        'users.updated_at',
        'users.latitude',
        'users.longitude',
        'm.id as message_id',
        'm.user_id as message_user_id',
        'm.receiver_id',
        'm.content',
        'm.created_at as message_created_at'
    )
    ->get();

        return response()->json(['data' => $messages], 200);
    }

    public function getChat(Request $request, $id)
    {
        $receiver_id = $id; // get selected user from query param
        $messages = collect();

        if ($receiver_id) {
            $messages = Message::with('user')
                ->where(function($q) use ($receiver_id) {
                    $q->where('user_id', Auth::id())
                      ->where('receiver_id', $receiver_id);
                })
                ->orWhere(function($q) use ($receiver_id) {
                    $q->where('user_id', $receiver_id)
                      ->where('receiver_id', Auth::id());   
                })
                ->latest()
                ->take(50)
                ->get()
                ->reverse();
        }

        return response()->json(['status' => 1, 'data' => $messages], 200);
    }

    public function send(Request $request)
    {
         $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->message,
        ]);

        // 🔹 Broadcast event via Reverb
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'Message sent',
            'message' => $message,
        ]);
    }

    public function history()
    {
        return response()->json(session()->get('gemini_chat', []));
    }
}
