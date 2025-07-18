<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = collect(User::all()->toArray());
        $users = $users->random();

        Post::factory(1)->create([
            "author_id" => $users["id"],
        ]);
    }
}
