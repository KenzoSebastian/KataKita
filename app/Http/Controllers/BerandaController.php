<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        $posts = Post::with(["author", "likes"])
            ->withCount(["likes", "comments"])
            ->latest("created_at")
            ->get();

        if (auth()->check()) {
            $activeUser = auth()
                ->user()
                ->load(["followers", "followings"]);
            return view("pages.main", compact(["activeUser", "posts"]));
        }

        return view("pages.main", compact(["posts"]));
    }

    public function showPost($slug)
    {
        $post = Post::with(["author", "likes"])
            ->withCount(["likes", "comments"])
            ->where("slug", $slug)
            ->get()
            ->firstOrFail();
            
        if (auth()->check()) {
            $activeUser = auth()
                ->user()
                ->load(["followers", "followings"]);
            return view("pages.showPost", compact(["activeUser", "post"]));
        }
        
        return view("pages.showPost", compact("post"));
    }

    public function likePost($slug)
    {
        $user = auth()->user();
        try {
            $post = Post::where("slug", $slug)->firstOrFail();

            if ($post->likes()->where("user_id", $user->id)->exists()) {
                // delete the like
                $post->likes()->where("user_id", $user->id)->delete();
                return response()->json([
                    "message" => "Post unliked successfully",
                    "liked" => false,
                ]);
            }

            $post->likes()->create([
                "user_id" => $user->id,
            ]);

            return response()->json([
                "message" => "Post liked successfully",
                "liked" => true,
            ]);
        } catch (\Exception $e) {
            $liked = $post->likes()->where("user_id", $user->id)->exists();
            return response()->json([
                "message" => "Failed to like the post",
                "liked" => $liked,
            ]);
        }
    }
}
