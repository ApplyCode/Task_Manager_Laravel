<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Patient;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = User::all()->pluck('id')->toArray();
        $patientIds = Patient::all()->pluck('id')->toArray();

        $statusArray = ['Cancelled', 'Not Started', 'In Progress', 'On Hold', 'Completed'];
        $appointment_progress = $this->faker->numberBetween(0, 100);

        return [
            'user_id' => $this->faker->randomElement($userIds),
            'title' => $this->faker->sentence,
            'squad_id' => 'd5ade74a-06d1-11ee-be56-0242ac120002',
            'description' => $this->faker->paragraph,
            'appointment_progress' => $appointment_progress,
            'completed' => $appointment_progress == 100 ? true : false,
            'status' => $this->faker->randomElement($statusArray),
            'category' => 'Uncategorized',
            'public' => $this->faker->boolean,
            'team' => $this->faker->randomElements($userIds, $this->faker->numberBetween(0, 10)),
            'cost' => $this->faker->numberBetween(100, 200),
            'patient_id' => $this->faker->randomElement($patientIds),
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
