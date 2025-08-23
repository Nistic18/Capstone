<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(10);
        return view('newsfeed.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048'
        ]);

        $path = $request->file('image')?->store('posts', 'public');

        Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'image' => $path,
        ]);

        return back()->with('success', 'Post created successfully!');
    }
    public function react(Request $request, Post $post)
{
    $request->validate([
        'type' => 'required|in:love,laugh,wow'
    ]);

    $reaction = $post->reactions()->updateOrCreate(
        ['user_id' => auth()->id()],
        ['type' => $request->type]
    );

    return back();
}
public function comment(Request $request, Post $post)
{
    $request->validate([
        'content' => 'required|string|max:500',
    ]);

    $post->comments()->create([
        'user_id' => auth()->id(),
        'content' => $request->content,
    ]);

    return back();
}
public function create()
{
    return view('newsfeed.post'); // Show the separate form page
}
public function show(Post $post)
{
    $post->load(['user', 'comments.user', 'reactions']);
    return view('newsfeed.show', compact('post'));
}

}
