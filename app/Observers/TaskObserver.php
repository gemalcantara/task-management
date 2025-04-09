<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->updateParentTaskStatus($task);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Check if status was changed
        if ($task->isDirty('status')) {
            $this->updateParentTaskStatus($task);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->updateParentTaskStatus($task);
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        $this->updateParentTaskStatus($task);
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        $this->updateParentTaskStatus($task);
    }
    
    /**
     * Update parent task status if all subtasks are done
     */
    private function updateParentTaskStatus(Task $task): void
    {
        // Only process if this is a subtask
        if (!$task->parent_id) {
            return;
        }
        
        $parent = Task::find($task->parent_id);
        if ($parent && $parent->areAllSubtasksDone() && $parent->status !== 'done') {
            $parent->status = 'done';
            // Skip observer to prevent infinite loop
            $parent->saveQuietly();
        }
    }
}
