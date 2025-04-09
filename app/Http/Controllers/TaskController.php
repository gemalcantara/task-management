<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        //
        $perPage = $request->input('paginate', 6); // Default to 6 if not provided
        $query = Task::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }else{
            $query->orderBy('created_at', 'desc');
        }

        $tasks = $query->mainTask()->ownTask()->with('subtask')->paginate($perPage);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request)
    {
        $dropdowns = [
            'status' => [
                'to_do' => 'To Do',
                'in_progress' => 'In Progress',
                'done' => 'Done',
            ],
            'visibility' => [
                'draft' => 'Draft',
                'published' => 'Published',
            ],
        ];
        
        // Get parent tasks for dropdown
        $parentTasks = Task::mainTask()->ownTask()->pluck('title', 'id');
        
        // Handle parent_id parameter when creating a subtask from the show page
        $selectedParentId = $request->parent_id;
        $parentTask = null;
        
        if ($selectedParentId) {
            $parentTask = Task::find($selectedParentId);
        }
        
        return view('tasks.create', compact('dropdowns', 'parentTasks', 'selectedParentId', 'parentTask'));
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request)
    {
        // Create new task
        $task = new Task($request->validated());
        $task->user_id = auth()->id();
        
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('tasks', 'public');
            $task->image = 'storage/' . $path;
        }
        
        $task->save();
        
        // Determine redirect based on whether the task is a subtask
        if ($task->parent_id) {
            return redirect()->route('tasks.show', $task->parent_id)
                ->with('success', 'Subtask created successfully.');
        }
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        // Load subtasks for this task if any
        $subtasks = $task->subtask()->ownTask()->with('user')->get();
        
        return view('tasks.show', compact('task', 'subtasks'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $dropdowns = [
            'status' => [
                'to_do' => 'To Do',
                'in_progress' => 'In Progress',
                'done' => 'Done',
            ],
            'visibility' => [
                'draft' => 'Draft',
                'published' => 'Published',
            ],
        ];
        
        // Get parent tasks for dropdown (excluding itself and its children)
        $parentTasks = Task::whereNull('parent_id')
            ->where('id', '!=', $task->id)
            ->pluck('title', 'id');
            
        return view('tasks.edit', compact('task', 'dropdowns', 'parentTasks'));
    }

    /**
     * Update the specified resource in tas.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->fill($request->validated());
        
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($task->image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $task->image));
            }
            
            // Store new image
            $path = $request->file('image')->store('tasks', 'public');
            $task->image = 'storage/' . $path;
        }
        
        $task->save();
        
        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
        if ($task->image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $task->image));
        }
        
        // Get parent ID before deleting for proper redirect
        $parentId = $task->parent_id;
        
        $task->delete();
        
        // If this was a subtask, redirect to the parent task's show page
        if ($parentId) {
            return redirect()->route('tasks.show', $parentId)
                ->with('success', 'Subtask deleted successfully.');
        }
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Toggle task status between to_do, in_progress, and done.
     */
    public function toggleStatus(Request $request, Task $task)
    {
        $currentStatus = $task->status;
        
        // Define next status in the workflow
        $statusFlow = [
            'to_do' => 'in_progress',
            'in_progress' => 'done',
            'done' => 'to_do'
        ];
        
        // If a specific status is requested, use that
        if ($request->has('status') && in_array($request->status, array_keys($statusFlow))) {
            $task->status = $request->status;
        } else {
            // Otherwise, move to next status in flow
            $task->status = $statusFlow[$currentStatus] ?? 'to_do';
        }
        
        $task->save();
        
        return redirect()->back()->with('success', 'Task status updated to ' . ucwords(str_replace('_', ' ', $task->status)) . '.');
    }
    
    /**
     * Toggle task visibility between draft and published.
     */
    public function toggleVisibility(Task $task)
    {
        $task->visibility = $task->visibility === 'Draft' ? 'published' : 'draft';
        $task->save();
        
        return redirect()->back()->with('success', 'Task visibility changed to ' . ucfirst($task->visibility) . '.');
    }
}
