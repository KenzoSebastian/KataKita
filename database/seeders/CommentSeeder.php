<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->toArray();
        $posts = Post::all()->toArray();

        foreach ($users as $user) {
            foreach ($posts as $post) {
                Comment::factory()->create([
                    "user_id" => $user["id"],
                    "post_id" => $post["id"],
                ]);
            }
        }
    }
}
