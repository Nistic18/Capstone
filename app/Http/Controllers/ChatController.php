<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $receiver_id = $request->query('user'); // selected user

        $users = User::whereIn('id', function ($query) use ($userId) {
            $query->select('user_id')
                  ->from('messages')
                  ->where('receiver_id', $userId)
                  ->union(
                      DB::table('messages')
                        ->select('receiver_id')
                        ->where('user_id', $userId)
                  );
        })->where('id', '!=', $userId)
          ->get();

        $messages = collect();

        if ($receiver_id) {
            $messages = Message::with('user')
                ->where(function ($q) use ($receiver_id, $userId) {
                    $q->where('user_id', $userId)
                      ->where('receiver_id', $receiver_id);
                })
                ->orWhere(function ($q) use ($receiver_id, $userId) {
                    $q->where('user_id', $receiver_id)
                      ->where('receiver_id', $userId);
                })
                ->latest()
                ->take(50)
                ->get()
                ->reverse();
        }
        if ($receiver_id) {
    // Mark all messages from selected user as read
    Message::where('user_id', $receiver_id)
        ->where('receiver_id', $userId)
        ->where('is_read', 0)
        ->update(['is_read' => 1]);
}

        return view('message', compact('users', 'messages', 'receiver_id'));
    }

public function send(Request $request)
{
    $request->validate([
        'message' => 'nullable|string|max:1000',
        'image' => 'nullable|image|max:2048',
        'receiver_id' => 'required|exists:users,id',
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        // Destination folder in htdocs/img/chat_images
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/img/chat_images';

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        // Unique filename
        $filename = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();

        // Move file
        $request->file('image')->move($destination, $filename);

        // Save relative path for database
        $imagePath = 'img/chat_images/' . $filename;
    }

    Message::create([
        'user_id' => auth()->id(),
        'receiver_id' => $request->receiver_id,
        'content' => $request->message,
        'image' => $imagePath,
    ]);

    return back()->withInput();
}

    public function history()
    {
        return response()->json(session()->get('gemini_chat', []));
    }
}
