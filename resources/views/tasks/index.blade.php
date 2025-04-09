@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="GET" action="{{ route('tasks.index') }}">
            <div class="row mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by title"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="">Sort by</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)
                        </option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)
                        </option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest)
                        </option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest)
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Filter by status</option>
                        <option value="to-do" {{ request('status') == 'to-do' ? 'selected' : '' }}>To-Do</option>
                        <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In-Progress
                        </option>
                        <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="submit" class="btn btn-outline-dark"> <i class="bi bi-funnel"></i>
                            Filter
                        </button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Clear
                        </a>
                    </div>
                </div>
        </form>
        <div class="col-md-3">
            <div class="d-flex justify-content-end">
                <div class="me-2">
                    <a href="{{ route('tasks.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>
                        Create Task</a>
                </div>
                <div>
                    {{ $tasks->appends(request()->query())->onEachSide(2)->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach ($tasks as $task)
            <div class="col">
                <div class="card h-100">
                    <div class="card-img-container">
                        <img src="{{ $task->image_url }}" class="card-img-top" alt="{{ $task->title }}">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $task->title }}</h5>
                        <p class="card-text">{{ $task->content }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-body-secondary">Last updated {{ $task->updated_at }}</small>
                            <div class="btn-group" role="group" aria-label="Basic outlined example">
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $task->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <div class="modal fade" id="deleteModal{{ $task->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $task->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $task->id }}">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete "{{ $task->title }}"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
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
    </div>
@endsection
