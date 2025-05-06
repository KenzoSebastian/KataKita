<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\Following;
use App\Models\like;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.z
     */
    public function run(): void
    {
        User::create(attributes: [
            "id" => substr(md5(uniqid()), 0, 8),
            "fullname" => "Kenzo Sebastian",
            "username" => "kenzoCool123",
            "email" => "kenzosebastian4@gmail.com",
            "password" => Hash::make("Kenzo555"),
        ]);

        User::factory(count: 7)->has(Follower::factory(3))->has(Following::factory(3))->has(Post::factory(3))->create();

        $users = User::all()->toArray();

        foreach ($users as $user) {
            // Ambil semua pengguna lain (bukan user saat ini)
            $otherUsers = User::where("id", "!=", $user["id"])->get()->toArray();

            // Proses followers dan following
            foreach (["followers" => Follower::class, "following" => Following::class] as $type => $model) {
                $seeds = $model::where("user_id", $user["id"])->get()->toArray();

                foreach ($seeds as $seed) {
                    $randomUser = $otherUsers[array_rand($otherUsers)];
                    $model::where("id", $seed["id"])->update([
                        $type === "followers" ? "follower_id" : "Following_id" => $randomUser["id"],
                    ]);

                    // Hapus pengguna yang sudah dipilih untuk menghindari duplikasi
                    $otherUsers = array_filter($otherUsers, function ($otherUser) use ($randomUser) {
                        return $otherUser["id"] !== $randomUser["id"];
                    });
                }
            }
        }
        $this->call([LikeSeeder::class, CommentSeeder::class]);
    }
}
