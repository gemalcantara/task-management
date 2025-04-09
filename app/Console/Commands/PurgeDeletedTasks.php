<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class PurgeDeletedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:purge {days=30 : Number of days after which deleted tasks should be purged}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete tasks that were move to trash more than the specified number of days ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');
        $date = Carbon::now()->subDays($days);
        
        $this->info("Deleting tasks mark as trashed before {$date->format('Y-m-d H:i:s')}...");
        
        $count = Task::onlyTrashed()
            ->where('deleted_at', '<', $date)
            ->forceDelete();
        
        $this->info("Successfully deleted {$count} trashed tasks.");
        
        return Command::SUCCESS;
    }
}