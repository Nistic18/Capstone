<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Post::with(['user', 'comments.user', 'reactions'])->latest();

        // If not admin, show only approved posts + user's own posts
        if (!$user->is_admin) {
            $query->where(function ($q) use ($user) {
                $q->where('status', 'approved')
                    ->orWhere('user_id', $user->id);
        });
    }

        $posts = $query->paginate(10);

        return view('newsfeed.index', compact('posts'));
    }

    public function create()
    {
        return view('newsfeed.post'); // Show the separate form page
    }

public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string|max:1000',
        'image' => 'nullable|image|max:2048'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        // Destination folder
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/img/posts';
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();

        // Move the file
        $request->file('image')->move($destination, $filename);

        // Save relative path
        $imagePath = 'img/posts/' . $filename;
    }

    Post::create([
        'user_id' => auth()->id(),
        'title' => $request->title,
        'content' => $request->content,
        'image' => $imagePath,
        'status' => 'pending',
    ]);

    return redirect()->route('newsfeed.index')->with('success', 'Post created successfully!');
}

    public function show(Post $post)
    {
        $post->load(['user', 'comments.user', 'reactions']);
        return view('newsfeed.show', compact('post'));
    }

    public function edit(Post $post)
    {
        // Check if user owns the post
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('newsfeed.edit', compact('post'));
    }

public function update(Request $request, Post $post)
{
    // Check if user owns the post
    if (auth()->id() !== $post->user_id) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string|max:1000',
        'image' => 'nullable|image|max:2048',
        'remove_image' => 'nullable|boolean'
    ]);

    $data = [
        'title' => $request->title,
        'content' => $request->content,
    ];

    // Destination folder for images
    $destination = $_SERVER['DOCUMENT_ROOT'] . '/img/posts';
    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }

    // Handle image upload
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($post->image && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $post->image)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $post->image);
        }

        $filename = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move($destination, $filename);

        $data['image'] = 'img/posts/' . $filename;
    }

    // Handle image removal
    if ($request->has('remove_image') && $request->remove_image) {
        if ($post->image && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $post->image)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $post->image);
        }
        $data['image'] = null;
    }

    $post->update($data);

    return redirect()->route('newsfeed.index')->with('success', 'Post updated successfully!');
}

    public function destroy(Post $post)
    {
        // Check if user owns the post
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated image if exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        // Delete the post (this will cascade delete comments and reactions if set up properly)
        $post->delete();

        return redirect()->route('newsfeed.index')->with('success', 'Post deleted successfully!');
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
}