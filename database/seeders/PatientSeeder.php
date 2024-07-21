<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 3 patients for each user
        User::withoutGlobalScope('squad')->get()->each(function ($user) {
            Patient::factory()->count(3)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
