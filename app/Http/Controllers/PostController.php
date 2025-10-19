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

        $path = $request->file('image')?->store('posts', 'public');

        Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'image' => $path,
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

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
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