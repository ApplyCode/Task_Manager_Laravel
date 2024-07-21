<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Task;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = User::all()->pluck('id')->toArray();
        $taskIds = Task::all()->pluck('id')->toArray();

        return [
            'squad_id' => 'd5ade74a-06d1-11ee-be56-0242ac120002',
            'user_id' => $this->faker->randomElement($userIds),
            'task_id' => $this->faker->randomElement($taskIds),
            'body' => $this->faker->paragraph,
        ];
    }
}
