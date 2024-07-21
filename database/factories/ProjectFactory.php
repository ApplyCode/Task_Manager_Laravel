<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'user_id' => $this->faker->randomElement($userIds),
            'squad_id' => 'd5ade74a-06d1-11ee-be56-0242ac120002',
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];
    }
}
