@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-between mb-4">
            <div class="col-md-4">
                <h2>Tasks</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Create Task
                </a>
            </div>
        </div>
        <div class="row justify-content-between mb-4">
            <div class="col-md-8">
                <form action="{{ route('tasks.index') }}" method="GET" class="row row-cols-lg-auto g-3">
                    <div class="col-12">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search tasks" name="search"
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div class="col-12">
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="to_do" {{ request('status') === 'to_do' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <select class="form-select" name="sort" onchange="this.form.submit()">
                            <option value="">Sort by</option>
                            <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Title (A-Z)
                            </option>
                            <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Title (Z-A)
                            </option>
                            <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Date (Oldest)
                            </option>
                            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Date (Newest)
                            </option>
                        </select>
                    </div>
                    <div class="col-12">
                        <select class="form-select" name="paginate" onchange="this.form.submit()">
                            <option value="6"
                                {{ request('paginate') == 6 || !request('paginate') ? 'selected' : '' }}>6
                                per page</option>
                            <option value="12" {{ request('paginate') == 12 ? 'selected' : '' }}>12 per page</option>
                            <option value="24" {{ request('paginate') == 24 ? 'selected' : '' }}>24 per page</option>
                        </select>
                    </div>
                    @if (request('search') || request('status') || request('sort') || request('paginate'))
                        <div class="col-12">
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        @if ($tasks->count() > 0)
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($tasks as $task)
                    <div class="col">
                        <div class="card task h-100">
                            <div onclick="window.location='{{ route('tasks.show', $task->id) }}'">
                                <div class="card-img-container">
                                    <img src="{{ $task->image_url }}" class="card-img-top" alt="{{ $task->title }}">
                                    <div class="status-badge position-absolute top-0 end-0 m-2">
                                        <span
                                            class="badge bg-{{ $task->status == 'to_do' ? 'secondary' : ($task->status == 'in_progress' ? 'primary' : 'success') }}">
                                            {{ $task->status == 'to_do' ? 'To Do' : ($task->status == 'in_progress' ? 'In Progress' : 'Done') }}
                                        </span>
                                        <span
                                            class="badge bg-{{ strtolower($task->visibility) == 'draft' ? 'warning' : 'info' }}">
                                            {{ $task->visibility }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $task->title }}</h5>
                                    <p class="card-text">{{ $task->content }}</p>

                                    @if ($task->subtask->count() > 0)
                                        @php
                                            $progress = $task->getSubtaskProgress();
                                        @endphp
                                        <div class="mt-3">
                                            <small>Subtasks: {{ $progress['completed'] }}/{{ $progress['total'] }}</small>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar {{ $progress['percent'] == 100 ? 'bg-success' : 'bg-primary' }}"
                                                    role="progressbar" style="width: {{ $progress['percent'] }}%"
                                                    aria-valuenow="{{ $progress['percent'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                </div>
                            </div>
                            <div class="card-footer">
                                <small class="text-body-secondary">Last updated {{ $task->updated_at }}</small>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group mt-3" role="group">
                                        <form action="{{ route('tasks.toggle-status', $task->id) }}" method="POST"
                                            class="me-1">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-{{ $task->status == 'to_do' ? 'secondary' : ($task->status == 'in_progress' ? 'primary' : 'success') }}">
                                                <i
                                                    class="bi bi-{{ $task->status == 'to_do' ? 'circle' : ($task->status == 'in_progress' ? 'hourglass-split' : 'check-circle') }}"></i>
                                                    Mark as {{ $task->status == 'to_do' ? 'In Progress' : ($task->status == 'in_progress' ? 'Done' : 'To Do') }}
                                            </button>
                                        </form>

                                        <form action="{{ route('tasks.toggle-visibility', $task->id) }}" method="POST"
                                            class="me-1">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-{{ strtolower($task->visibility) == 'draft' ? 'warning' : 'info' }}">
                                                <i
                                                    class="bi bi-{{ strtolower($task->visibility) == 'draft' ? 'file-earmark' : 'globe' }}"></i>
                                                    Mark as {{ strtolower($task->visibility) == 'draft' ? 'Published' : 'Draft' }}
                                            </button>
                                        </form>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tasks.edit', $task->id) }}"
                                            class="btn  btn-outline-primary"><i
                                                class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn  btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $task->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="deleteModal{{ $task->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $task->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $task->id }}">
                                            Confirm Remove</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to remove "{{ $task->title }}"?
                                        @if ($task->subtask->count() > 0)
                                            <div class="alert alert-warning mt-2">
                                                <i class="bi bi-exclamation-triangle"></i> Warning: This will also remove
                                                {{ $task->subtask->count() }} subtask(s).
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
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
            <div class="mt-4 d-flex justify-content-center">
                {{ $tasks->withQueryString()->links() }}
            </div>
        @else
            <div class="alert alert-info">
                No tasks found. <a href="{{ route('tasks.create') }}">Create a new task</a>.
            </div>
        @endif
    </div>
@endsection