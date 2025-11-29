<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostSupplier;
use Illuminate\Support\Facades\Storage;

class SupplierPostController extends Controller
{
    public function index()
    {
        // Show all approved posts to everyone (no filtering needed)
        $posts = PostSupplier::with(['user', 'comments.user', 'reactions'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(10);

        return view('newsfeedsupplier.index', compact('posts'));
    }

    public function create()
    {
        // Only admins can create posts
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action. Only administrators can create posts.');
        }

        return view('newsfeedsupplier.post');
    }

    public function store(Request $request)
    {
        // Only admins can store posts
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action. Only administrators can create posts.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048'
        ]);

        $path = $request->file('image')?->store('posts', 'public');

        PostSupplier::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'image' => $path,
            'status' => 'approved', // Auto-approve all posts
        ]);

        return redirect()->route('newsfeedsupplier.index')->with('success', 'Post created successfully!');
    }

    public function show(PostSupplier $post)
    {
        $post->load(['user', 'comments.user', 'reactions']);
        return view('newsfeedsupplier.show', compact('post'));
    }

    public function edit(PostSupplier $post)
    {
        // Only admins can edit posts
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action. Only administrators can edit posts.');
        }

        return view('newsfeedsupplier.edit', compact('post'));
    }

    public function update(Request $request, PostSupplier $post)
    {
        // Only admins can update posts
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action. Only administrators can update posts.');
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

        return redirect()->route('newsfeedsupplier.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(PostSupplier $post)
    {
        // Only admins can delete posts
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action. Only administrators can delete posts.');
        }

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('newsfeedsupplier.index')->with('success', 'Post deleted successfully!');
    }

    public function react(Request $request, PostSupplier $post)
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

    public function comment(Request $request, PostSupplier $post)
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

    // New method for toggling featured status
    public function toggleFeatured(PostSupplier $post)
    {
        // Only admin can feature posts
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        // If featuring this post, unfeatured all others first
        if (!$post->is_featured) {
            PostSupplier::where('is_featured', true)->update(['is_featured' => false]);
        }

        $post->is_featured = !$post->is_featured;
        $post->save();

        $message = $post->is_featured 
            ? 'Post featured on landing page successfully!' 
            : 'Post removed from landing page.';

        return back()->with('success', $message);
    }
}