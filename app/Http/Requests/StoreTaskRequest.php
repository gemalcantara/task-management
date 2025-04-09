<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100|unique:tasks',
            'content' => 'required|string',
            'status' => 'required|string|in:to_do,in_progress,done',
            'visibility' => 'required|string|in:draft,published',
            'image' => 'nullable|image|max:4096',
            'parent_id' => 'nullable|exists:tasks,id',
        ];
    }
}
