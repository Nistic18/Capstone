<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())->get(); // all users except self

        $receiver_id = $request->query('user'); // get selected user from query param
        $messages = collect();

        if ($receiver_id) {
            $messages = Message::with('user')
                ->where(function($q) use ($receiver_id) {
                    $q->where('user_id', auth()->id())
                      ->where('receiver_id', $receiver_id);
                })
                ->orWhere(function($q) use ($receiver_id) {
                    $q->where('user_id', $receiver_id)
                      ->where('receiver_id', auth()->id());
                })
                ->latest()
                ->take(50)
                ->get()
                ->reverse();
        }

        return view('chat', compact('users', 'messages', 'receiver_id'));
    }

public function send(Request $request)
{
    $request->validate([
        'message' => 'nullable|string|max:1000',
        'image' => 'nullable|image|max:2048', // max 2MB
        'receiver_id' => 'required|exists:users,id',
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('chat_images', 'public');
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
