<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'title' => $this->faker->sentence, // 랜덤 문장 생성
            'content' => $this->faker->paragraph, // 랜덤 문단 생성
            'user_id' => User::factory(), // User 모델과 연동된 랜덤 user_id 생성
        ];
    }
}
