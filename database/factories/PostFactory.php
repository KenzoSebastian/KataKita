<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = substr(md5(uniqid()), 0, 8);
        $slug = implode('-', str_split(preg_replace('/[\$\/0-9.,]/', '', bcrypt($id)), 10));
        return [
            'id' => $id,
            'slug' => $slug,
            'content' => $this->faker->text(1000),
        ];
    }
}
