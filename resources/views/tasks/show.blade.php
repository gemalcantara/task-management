@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card task-view mb-4 d-flex flex-column">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="{{ $task->image_url }}" class="img-fluid rounded-start" alt="{{ $task->title }}">
                </div>
                <div class="col-md-8 d-flex flex-column">
                    <div class="card-body flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="card-title">{{ $task->title }}</h2>
                            <div>
                                <span
                                    class="badge bg-{{ $task->status == 'to_do' ? 'secondary' : ($task->status == 'in_progress' ? 'primary' : 'success') }}">
                                    {{ $task->status == 'to_do' ? 'To Do' : ($task->status == 'in_progress' ? 'In Progress' : 'Done') }}
                                </span>
                                <span class="badge bg-{{ strtolower($task->visibility) == 'draft' ? 'warning' : 'info' }}">
                                    {{ $task->visibility }}
                                </span>
                            </div>
                        </div>
                        <p class="card-text">{{ $task->content }}</p>

                        @if ($task->parent)
                            <p class="card-text"><small class="text-muted">Parent Task:
                                    <a href="{{ route('tasks.show', $task->parent->id) }}">{{ $task->parent->title }}</a>
                                </small></p>
                        @endif

                        @if (!$task->parent_id && $subtasks->count() > 0)
                            @php
                                $progress = $task->getSubtaskProgress();
                            @endphp
                            <div class="mt-3">
                                <h5>Subtask Progress: {{ $progress['completed'] }}/{{ $progress['total'] }} completed</h5>
                                <div class="progress">
                                    <div class="progress-bar {{ $progress['percent'] == 100 ? 'bg-success' : 'bg-primary' }}"
                                        role="progressbar" style="width: {{ $progress['percent'] }}%"
                                        aria-valuenow="{{ $progress['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $progress['percent'] }}%
                                    </div>
                                </div>
                            </div>
                        @endif

                        <p class="card-text"><small class="text-muted">Created by:
                                {{ $task->user->name ?? 'Unknown' }}</small></p>
                        <p class="card-text"><small class="text-muted">Created:
                                {{ $task->created_at->format('Y-m-d H:i:s') }}</small></p>
                        <p class="card-text"><small class="text-muted">Last updated: {{ $task->updated_at }}</small></p>
                    </div>
                    <div class="card-footer mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    <i class="bi bi-trash"></i> Move to trash
                                </button>
                                @if ($task->parent_id)
                                    <a href="{{ route('tasks.show', $task->parent_id) }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to Parent
                                    </a>
                                @else
                                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to List
                                    </a>
                                @endif
                            </div>
                            {{-- Task status and visibility toggle buttons --}}
                            <div class="btn-group" role="group">
                                <form action="{{ route('tasks.toggle-status', $task->id) }}" method="POST" class="me-1">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-outline-{{ $task->status == 'to_do' ? 'secondary' : ($task->status == 'in_progress' ? 'primary' : 'success') }}">
                                        <i
                                            class="bi bi-{{ $task->status == 'to_do' ? 'circle' : ($task->status == 'in_progress' ? 'hourglass-split' : 'check-circle') }}"></i>
                                        Mark as
                                        {{ $task->status == 'to_do' ? 'In Progress' : ($task->status == 'in_progress' ? 'Done' : 'To Do') }}
                                    </button>
                                </form>

                                <form action="{{ route('tasks.toggle-visibility', $task->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-outline-{{ strtolower($task->visibility) == 'draft' ? 'warning' : 'info' }}">
                                        <i
                                            class="bi bi-{{ strtolower($task->visibility) == 'draft' ? 'file-earmark' : 'globe' }}"></i>
                                        Mark as {{ strtolower($task->visibility) == 'draft' ? 'Publish' : 'Draft' }}
                                    </button>
                                </form>
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

        @if ($subtasks->count() > 0)
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($subtasks as $subtask)
                    <div class="col">
                        <div class="card task h-100">
                            <div onclick="window.location='{{ route('tasks.show', $subtask->id) }}'">
                                <div class="card-img-container">
                                    <img src="{{ $subtask->image_url }}" class="card-img-top" alt="{{ $subtask->title }}">
                                    <div class="status-badge position-absolute top-0 end-0 m-2">
                                        <span
                                            class="badge bg-{{ $subtask->status == 'to_do' ? 'secondary' : ($subtask->status == 'in_progress' ? 'primary' : 'success') }}">
                                            {{ $subtask->status == 'to_do' ? 'To Do' : ($subtask->status == 'in_progress' ? 'In Progress' : 'Done') }}
                                        </span>
                                        <span
                                            class="badge bg-{{ strtolower($subtask->visibility) == 'draft' ? 'warning' : 'info' }}">
                                            {{ $subtask->visibility }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $subtask->title }}</h5>
                                    <p class="card-text">{{ $subtask->content }}</p>
                                </div>
                            </div>
                            <div class="card-footer">
                                <small class="text-body-secondary">Last updated {{ $subtask->updated_at }}</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group mt-2" role="group">
                                        <form action="{{ route('tasks.toggle-status', $subtask->id) }}" method="POST"
                                            class="me-1">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-{{ $subtask->status == 'to_do' ? 'secondary' : ($subtask->status == 'in_progress' ? 'primary' : 'success') }}">
                                                <i
                                                    class="bi bi-{{ $subtask->status == 'to_do' ? 'circle' : ($subtask->status == 'in_progress' ? 'hourglass-split' : 'check-circle') }}"></i>
                                                Mark as
                                                {{ $subtask->status == 'to_do' ? 'In Progress' : ($subtask->status == 'in_progress' ? 'Done' : 'To Do') }}
                                            </button>
                                        </form>

                                        <form action="{{ route('tasks.toggle-visibility', $subtask->id) }}" method="POST"
                                            class="me-1">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-{{ strtolower($subtask->visibility) == 'draft' ? 'warning' : 'info' }}">
                                                <i
                                                    class="bi bi-{{ strtolower($subtask->visibility) == 'draft' ? 'file-earmark' : 'globe' }}"></i>
                                                Mark as
                                                {{ strtolower($subtask->visibility) == 'draft' ? 'Published' : 'Draft' }}
                                            </button>
                                        </form>
                                    </div>

                                    <div class="btn-group" role="group">

                                        <a href="{{ route('tasks.edit', $subtask->id) }}"
                                            class="btn btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $subtask->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="deleteModal{{ $subtask->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $subtask->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $subtask->id }}">
                                            Confirm Remove</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to remove "{{ $subtask->title }}"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('tasks.destroy', $subtask->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Move to
                                                trash</button>
                                        </form>
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

        <!-- Remove Modal for Main Task -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Remove</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to remove "{{ $task->title }}"?
                        @if ($subtasks->count() > 0)
                            <div class="alert alert-warning mt-2">
                                <i class="bi bi-exclamation-triangle"></i> Warning: This will also remove
                                {{ $subtasks->count() }} subtask(s).
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Move to trash</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
