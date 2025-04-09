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
     */
    public function run(): void
    {
        // Get existing users or create a default one if none exist
        $users = User::all();
        
        if ($users->isEmpty()) {
            // Create at least one user if there are none
            $users = collect([User::factory()->create()]);
        }
        
        // Create tasks associated with existing users
        $users->each(function ($user) {
            // Create 3-7 main tasks per user
            Task::factory()
                ->count(rand(3, 7))
                ->for($user)
                ->create()
                ->each(function ($task) {
                    // Add 0-3 subtasks for some main tasks
                    if (rand(0, 1)) {
                        Task::factory()
                            ->count(rand(0, 3))
                            ->for($task->user)
                            ->state([
                                'parent_id' => $task->id,
                            ])
                            ->create();
                    }
                });
        });

        // Create some additional tasks with specific statuses
        Task::factory()->count(3)->toDo()->create();
        Task::factory()->count(5)->inProgress()->create();
        Task::factory()->count(4)->done()->published()->create();
    }
}