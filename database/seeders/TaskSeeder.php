<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 5 tasks for each user
        User::withoutGlobalScope('squad')->get()->each(function ($user) {
            Task::factory()->count(5)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
