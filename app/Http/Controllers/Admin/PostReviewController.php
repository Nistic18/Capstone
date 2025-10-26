<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostSupplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class PostReviewController extends Controller
{
    public function index()
    {
        // Fetch pending community posts and add a 'post_type' attribute
        $communityPosts = Post::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($post) {
                $post->post_type = 'community';
                return $post;
            });

        // Fetch pending supplier posts and add a 'post_type' attribute
        $supplierPosts = PostSupplier::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($post) {
                $post->post_type = 'supplier';
                return $post;
            });

        // Combine both
        $allPosts = $communityPosts
            ->merge($supplierPosts)
            ->sortByDesc('created_at');

        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 10;
        $pagedData = $allPosts->slice(($page - 1) * $perPage, $perPage)->values();

        $pendingPosts = new LengthAwarePaginator(
            $pagedData,
            $allPosts->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.posts.review', compact('pendingPosts'));
    }

    public function approve(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'supplier') {
            $post = PostSupplier::findOrFail($id);
        } else {
            $post = Post::findOrFail($id);
        }

        $post->update(['status' => 'approved']);
        return back()->with('success', 'Post approved successfully!');
    }

    public function reject(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'supplier') {
            $post = PostSupplier::findOrFail($id);
        } else {
            $post = Post::findOrFail($id);
        }

        $post->update(['status' => 'rejected']);
        return back()->with('error', 'Post rejected.');
    }
}