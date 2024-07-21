<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project; 
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = User::all()->pluck('id')->toArray();
        $projectIds = Project::all()->pluck('id')->toArray();

        $statusArray = ['Cancelled', 'Not Started', 'In Progress', 'On Hold', 'Completed'];
        $priorityArray = ['None', 'Lowest', 'Low', 'Medium', 'High', 'Highest'] ;
        $task_progress = $this->faker->numberBetween(0, 100);

        return [
            'user_id' => $this->faker->randomElement($userIds),
            'title' => $this->faker->sentence,
            'squad_id' => 'd5ade74a-06d1-11ee-be56-0242ac120002',
            'description' => $this->faker->paragraph,
            'task_progress' => $task_progress,
            'completed' => $task_progress == 100 ? true : false,
            'priority' => $this->faker->randomElement($priorityArray),
            'status' => $this->faker->randomElement($statusArray),
            'category' => 'Uncategorized',
            'public' => $this->faker->boolean,
            'team' => $this->faker->randomElements($userIds, $this->faker->numberBetween(0, 10)),
            'project_id' => $this->faker->randomElement($projectIds),
            'assigned_to' => $this->faker->randomElement($userIds),
            'start_date' => new \MongoDB\BSON\UTCDateTime(
                $this->faker->dateTimeBetween('-1 month', '+1 month')->getTimestamp() * 1000
            ),
            'due_date' => new \MongoDB\BSON\UTCDateTime(
                $this->faker->dateTimeBetween('-1 month', '+1 month')->getTimestamp() * 1000
            ),
        ];
    }
}
