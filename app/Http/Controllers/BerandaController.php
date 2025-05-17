<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        $allPosts = Post::with(['author', 'likes'])
            ->withCount(['likes', 'comments'])
            ->latest('created_at')
            ->get();

        $allUser = User::all();

        if (auth()->check()) {
            $activeUser = auth()
                ->user()
                ->load(['followers', 'followings']);
            return view('pages.main', compact(['activeUser', 'allPosts', 'allUser']));
        }

        return view('pages.main', compact(['allPosts', 'allUser']));
    }

    public function showPost($slug)
    {
        $post = Post::with(['author', 'likes', 'comments'])
            ->withCount(['likes', 'comments'])
            ->where('slug', $slug)
            ->get()
            ->firstOrFail();

        $allUser = User::all();

        if (auth()->check()) {
            $activeUser = auth()
                ->user()
                ->load(['followers', 'followings']);
            return view('pages.showPost', compact(['activeUser', 'post', 'allUser']));
        }

        return view('pages.showPost', compact('post', 'allUser'));
    }

    public function createPost(Request $request)
    {
        $validation = $request->validate(
            [
                'id' => 'required',
                'content' => 'required|string',
                'author_id' => 'required|exists:users,id',
                'slug' => 'required|unique:posts,slug',
                'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'content.required' => 'Content is required',
                'content.string' => 'Content must be a string',
                'author_id.required' => 'Author ID is required',
                'author_id.exists' => 'Author ID must exist in users table',
                'slug.required' => 'Slug is required',
                'slug.unique' => 'Slug must be unique',
                'image.mimes' => 'Image must be a jpeg, png, jpg, gif, or svg',
                'image.max' => 'Image must be less than 2MB',
            ],
        );
        try {
            if ($request->hasFile('image')) {
                $storeFile = $request->file('image')->store('post-images');
                $validation['image'] = 'storage/' . $storeFile;
            }

            Post::create($validation);

            return redirect()->route('beranda')->with('success', 'Post created successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to create post. Please try again.']);
        }
    }

    public function likePost($slug)
    {
        $user = auth()->user();
        try {
            $post = Post::where('slug', $slug)->firstOrFail();

            if ($post->likes()->where('user_id', $user->id)->exists()) {
                // delete the like
                $post->likes()->where('user_id', $user->id)->delete();
                return response()->json([
                    'message' => 'Post unliked successfully',
                    'liked' => false,
                ]);
            }

            $post->likes()->create([
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Post liked successfully',
                'liked' => true,
            ]);
        } catch (\Exception $e) {
            $liked = $post->likes()->where('user_id', $user->id)->exists();
            return response()->json([
                'message' => 'Failed to like the post',
                'liked' => $liked,
            ]);
        }
    }

    public function commentPost(Request $request, $slug)
    {
        $validation = $request->validate(
            [
                'content' => 'required|string',
                'post_id' => 'required|exists:posts,id',
                'user_id' => 'required|exists:users,id',
            ],
            [
                'content.required' => 'Comment is required',
                'content.string' => 'Comment must be a string',
                'post_id.required' => 'Post ID is required',
                'post_id.exists' => 'Post ID must exist in posts table',
                'user_id.required' => 'User ID is required',
                'user_id.exists' => 'User ID must exist in users table',
            ],
        );

        try {
            $post = Post::where('slug', $slug)->firstOrFail();
            $post->comments()->create($validation);

            return redirect()
                ->route('show-post', ['slug' => $slug])
                ->with('success', 'Comment added successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to add comment. Please try again.']);
        }
    }
}
