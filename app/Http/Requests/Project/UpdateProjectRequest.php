<?php

//Soo Yu Hung

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest {

    protected function prepareForValidation() {
        $this->merge([
            'roles' => $this->roles ?? [],
            'members' => $this->members ?? [],
        ]);
    }

    public function rules() {
        $user = auth()->user();

        if ($user && ($user->role == 'admin' || $user->role == 'manager')) {
            return [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'budget_id' => 'required|exists:budgets,id',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id',
                'roles' => 'nullable|array',
                'roles.*' => 'in:Junior,Senior,Project Manager',
            ];
        } else {
            return [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'budget_id' => 'required|exists:budgets,id',
            ];
        }
    }

    public function messages() {
        return [
            'members.*.exists' => 'One or more members do not exist.',
            'roles.*.in' => 'Invalid role specified.',
        ];
    }
}
