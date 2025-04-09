<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the current user is the owner of the task
        return $this->task->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100|unique:tasks,title,' . $this->route('task')->id,
            'content' => 'nullable|string',
            'status' => 'required|string|in:to_do,in_progress,done',
            'visibility' => 'required|string|in:draft,published',
            'parent_id' => 'nullable|exists:tasks,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }
}
