<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 5 comments for each user
        User::withoutGlobalScope('squad')->get()->each(function ($user) {
            Comment::factory()->count(5)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
