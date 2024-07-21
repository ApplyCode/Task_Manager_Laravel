<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemindJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // gets all the tasks with a reminder date of today
        $tasks = Task::whereDate('reminder_date', new \MongoDB\BSON\UTCDateTime(strtotime('today') * 1000))->get();
        // for each task, send a notification to the assignee
        foreach ($tasks as $task) {
            $user = User::find($task->assigned_to);
            $user->notify(new TaskReminder($task));
        }

    }
}
