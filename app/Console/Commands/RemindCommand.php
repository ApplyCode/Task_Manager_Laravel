<?php

namespace App\Console\Commands;

use App\Jobs\RemindJob;
use Illuminate\Console\Command;

class RemindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remembers the users to complete the tasks for the day.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // runs the reminder job
        RemindJob::dispatch();
        return Command::SUCCESS;
    }
}
