<?php
// Jeremy
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskFilterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['nullable','string','|max:255'],
            'status' => ['nullable','string','in:Pending,In Progress,Completed'],
            'user_id' => ['nullable','exists:users,id'],
            'project_id' => ['nullable','exists:projects,id'],
            'priority' => ['nullable','string','in:low,medium,high'],
            'due_date' => ['nullable','date'],
        ];
    }
}
