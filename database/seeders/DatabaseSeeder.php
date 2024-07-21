<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::factory(10)->create();
        $this->call([ProjectSeeder::class, TaskSeeder::class, CommentSeeder::class]);
        $this->call([PatientSeeder::class, AppointmentSeeder::class]);
    }
}
