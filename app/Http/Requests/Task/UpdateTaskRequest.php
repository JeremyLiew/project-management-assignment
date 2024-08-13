<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'project_id' => ['required', 'exists:projects,id'],
            'expense_id' => ['nullable', 'exists:expenses,id'],
        ];
    }
}
