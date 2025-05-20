<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posts = Post::with(['author', 'likes'])
                ->withCount(['likes', 'comments'])
                ->latest('created_at')
                ->get();
            // jika data post kosong
            if ($posts->isEmpty()) {
                return response()->json([
                    'message' => 'No posts found',
                    'error' => true,
                ]);
            }
            if (auth()->check()) {
                $activeUser = auth()
                    ->user()
                    ->load(['followers', 'followings']);

                return view('components.post-card', compact('posts', 'activeUser'));
            }
            return view('components.post-card', compact('posts'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch posts',
                'error' => true,
            ]);
        }
    }

    public function postsByUser($id)
{
    try {
        $posts = Post::with(['author', 'likes'])
            ->withCount(['likes', 'comments'])
            ->where('author_id', $id)
            ->latest('created_at')
            ->get();

        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No posts found',
                'error' => true,
            ]);
        }

        // Jika ingin kirim activeUser juga (jika login)
        if (auth()->check()) {
            $activeUser = auth()->user()->load(['followers', 'followings']);
            return view('components.post-card', compact('posts', 'activeUser'));
        }

        return view('components.post-card', compact('posts'));
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to fetch user posts',
            'error' => true,
        ]);
    }
}

    public function postByFollowing()
    {
        try {
            // Pastikan user sudah login
            $user = auth()->user();
            if (!$user) {
                return response()->json(
                    [
                        'message' => 'Unauthorized',
                        'error' => true,
                    ],
                    401,
                );
            }

            $activeUser = $user->load(['followers', 'followings']);

            // Ambil ID user yang di-follow
            $followingId = $activeUser->followings->pluck('following_id');
            // Ambil post dari user yang di-follow saja
            $posts = Post::with(['author', 'likes'])
                ->withCount(['likes', 'comments'])
                ->whereIn('author_id', $followingId)
                ->latest('created_at')
                ->get();

            // jika data post kosong
            if ($posts->isEmpty()) {
                return response()->json([
                    'message' => 'No posts found',
                    'error' => true,
                ]);
            }

            return view('components.post-card', compact('posts', 'activeUser'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch following posts',
                'error' => true,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
                ->with(['error' => 'Failed to create post. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
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

    /**
     * Like a post
     */

    public function likePost($slug)
    {
        try {
            $user = auth()->user();
            $post = Post::where('slug', $slug)->firstOrFail();
            $liked = $post->likes()->where('user_id', $user->id)->exists();
            if ($liked) {
                // delete the like
                $post->likes()->where('user_id', $user->id)->delete();
                return response()->json([
                    'message' => 'Post unliked successfully',
                    'error' => false,
                ]);
            }

            $post->likes()->create([
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Post liked successfully',
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to like the post',
                'error' => true,
            ]);
        }
    }

    /**
     * Comment on a post
     */

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

            return redirect()->back()->with('success', 'Comment added successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with(['error' => 'Failed to add comment. Please try again.']);
        }
    }
}
