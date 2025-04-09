<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //
        $perPage = $request->input('paginate', 6); 
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
        
        $parentTasks = Task::mainTask()->ownTask()->pluck('title', 'id');
        
        $selectedParentId = $request->parent_id;
        $parentTask = null;
        
        if ($selectedParentId) {
            $parentTask = Task::find($selectedParentId);
        }
        
        return view('tasks.create', compact('dropdowns', 'parentTasks', 'selectedParentId', 'parentTask'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = new Task($request->validated());
        $task->user_id = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('tasks', 'public');
            $task->image = 'storage/' . $path;
        }
        
        $task->save();
        
        if ($task->parent_id) {
            return redirect()->route('tasks.show', $task->parent_id)
                ->with('success', 'Subtask created successfully.');
        }
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }
    public function show(Task $task)
    {
        $subtasks = $task->subtask()->ownTask()->with('user')->get();
        
        return view('tasks.show', compact('task', 'subtasks'));
    }

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
        
        $parentTasks = Task::whereNull('parent_id')
            ->where('id', '!=', $task->id)
            ->pluck('title', 'id');
            
        return view('tasks.edit', compact('task', 'dropdowns', 'parentTasks'));
    }
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->fill($request->validated());
        
        if ($request->hasFile('image')) {
            if ($task->image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $task->image));
            }
            $path = $request->file('image')->store('tasks', 'public');
            $task->image = 'storage/' . $path;
        }
        
        $task->save();
        
        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $task->image));
        }
        
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

    public function toggleStatus(Request $request, Task $task)
    {
        $currentStatus = $task->status;
        
        $statusFlow = [
            'to_do' => 'in_progress',
            'in_progress' => 'done',
            'done' => 'to_do'
        ];
        
        if ($request->has('status') && in_array($request->status, array_keys($statusFlow))) {
            $task->status = $request->status;
        } else {
            $task->status = $statusFlow[$currentStatus] ?? 'to_do';
        }
        
        $task->save();
        
        return redirect()->back()->with('success', 'Task status updated to ' . ucwords(str_replace('_', ' ', $task->status)) . '.');
    }
    
    public function toggleVisibility(Task $task)
    {
        $task->visibility = $task->visibility === 'Draft' ? 'published' : 'draft';
        $task->save();
        
        return redirect()->back()->with('success', 'Task visibility changed to ' . ucfirst($task->visibility) . '.');
    }
}
