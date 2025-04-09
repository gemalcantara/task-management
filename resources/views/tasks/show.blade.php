@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ $task->image_url }}" class="img-fluid rounded-start" alt="{{ $task->title }}">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title">{{ $task->title }}</h2>
                        <div>
                            <span class="badge bg-{{ $task->status == 'to_do' ? 'secondary' : ($task->status == 'in_progress' ? 'primary' : 'success') }}">
                                {{ $task->status == 'to_do' ? 'To Do' : ($task->status == 'in_progress' ? 'In Progress' : 'Done') }}
                            </span>
                            <span class="badge bg-info">{{ $task->visibility }}</span>
                        </div>
                    </div>
                    <p class="card-text">{{ $task->content }}</p>
                    
                    @if($task->parent)
                        <p class="card-text"><small class="text-muted">Parent Task: 
                            <a href="{{ route('tasks.show', $task->parent->id) }}">{{ $task->parent->title }}</a>
                        </small></p>
                    @endif
                    
                    <p class="card-text"><small class="text-muted">Created by: {{ $task->user->name ?? 'Unknown' }}</small></p>
                    <p class="card-text"><small class="text-muted">Created: {{ $task->created_at->format('Y-m-d H:i:s') }}</small></p>
                    <p class="card-text"><small class="text-muted">Last updated: {{ $task->updated_at }}</small></p>
                    
                    <div class="mt-3">
                        <div class="btn-group" role="group">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (!$task->parent_id)
        <div class="row mb-4">
            <div class="col-md-6">
                <h3>Subtasks</h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('tasks.create', ['parent_id' => $task->id]) }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Add Subtask
                </a>
            </div>
        </div>
    @endif

    @if($subtasks->count() > 0)
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($subtasks as $subtask)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-img-container">
                            <img src="{{ $subtask->image_url }}" class="card-img-top" alt="{{ $subtask->title }}">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $subtask->title }}</h5>
                            <p class="card-text">{{ $subtask->content }}</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-body-secondary">Last updated {{ $subtask->updated_at }}</small>
                                <div class="btn-group" role="group" aria-label="Basic outlined example">
                                    <a href="{{ route('tasks.show', $subtask->id) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('tasks.edit', $subtask->id) }}" class="btn btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $subtask->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <div class="modal fade" id="deleteModal{{ $subtask->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $subtask->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $subtask->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete "{{ $subtask->title }}"?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('tasks.destroy', $subtask->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if (!$task->parent_id)
            <div class="alert alert-info">
                No subtasks found for this task.
            </div>
            @endif
    @endif

    <!-- Delete Modal for Main Task -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete "{{ $task->title }}"?
                    @if($subtasks->count() > 0)
                        <div class="alert alert-warning mt-2">
                            <i class="bi bi-exclamation-triangle"></i> Warning: This will also delete {{ $subtasks->count() }} subtask(s).
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection