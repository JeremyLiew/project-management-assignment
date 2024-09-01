<?php
// Jeremy
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'string'],
            'status' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'expense_id' => ['nullable', 'exists:expenses,id'],
        ];
    }

    public function messages()
    {
        return [
            'project_id.exists' => 'The selected project does not exist.',
            'user_id.exists' => 'The selected user does not exist.',
        ];
    }
}
