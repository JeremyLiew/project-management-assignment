<?php
// Jeremy
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'project_id' => ['required', 'exists:projects,id'],
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
