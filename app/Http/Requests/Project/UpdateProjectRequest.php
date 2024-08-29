<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget_id' => 'required|exists:budgets,id',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'in:Junior,Senior,Project Manager',
        ];
    }

    public function messages()
    {
        return [
            'members.*.exists' => 'One or more members do not exist.',
            'roles.*.in' => 'Invalid role specified.',
        ];
    }
}