<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 5 projects for each user
        User::withoutGlobalScope('squad')->get()->each(function ($user) {
            Project::factory()->count(5)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
