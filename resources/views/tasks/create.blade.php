@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ isset($parentTask) ? __('Create New Subtask') : __('Create New Task') }}
                        @if(isset($parentTask))
                            <span class="text-muted"> for "{{ $parentTask->title }}"</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="title" class="col-md-4 col-form-label text-md-end">{{ __('Title') }}</label>
                                <div class="col-md-6">
                                    <input id="title" type="text"
                                        class="form-control @error('title') is-invalid @enderror" name="title"
                                        value="{{ old('title') }}" required autocomplete="title" autofocus>

                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="content"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Content') }}</label>

                                <div class="col-md-6">
                                    <textarea id="content"
                                        class="form-control @error('content') is-invalid @enderror" name="content"
                                        rows="5">{{ old('content') }}</textarea>

                                    @error('content')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="status"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Status') }}</label>

                                <div class="col-md-6">
                                    <select id="status" class="form-select @error('status') is-invalid @enderror" 
                                        name="status" required>
                                        <option value="">Select Status</option>
                                        @foreach($dropdowns['status'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="visibility"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Visibility') }}</label>

                                <div class="col-md-6">
                                    <select id="visibility" class="form-select @error('visibility') is-invalid @enderror" 
                                        name="visibility" required>
                                        <option value="">Select Visibility</option>
                                        @foreach($dropdowns['visibility'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('visibility') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('visibility')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="parent_id"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Parent Task') }}</label>

                                <div class="col-md-6">
                                    <select id="parent_id" class="form-select @error('parent_id') is-invalid @enderror" 
                                        name="parent_id" {{ isset($selectedParentId) ? 'disabled' : '' }}>
                                        <option value="">No Parent (Main Task)</option>
                                        @foreach($parentTasks as $id => $title)
                                            <option value="{{ $id }}" {{ old('parent_id', $selectedParentId) == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    @if(isset($selectedParentId))
                                        <input type="hidden" name="parent_id" value="{{ $selectedParentId }}">
                                        <p class="text-muted small mt-1">
                                            Creating subtask for: {{ $parentTask->title }}
                                        </p>
                                    @endif

                                    @error('parent_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="image"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Task Image') }}</label>

                                <div class="col-md-6">
                                    <input id="image" type="file"
                                        class="form-control @error('image') is-invalid @enderror" name="image">

                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($parentTask) ? __('Create Subtask') : __('Create Task') }}
                                    </button>
                                    <a href="{{ isset($parentTask) ? route('tasks.show', $parentTask->id) : route('tasks.index') }}" class="btn btn-secondary">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
