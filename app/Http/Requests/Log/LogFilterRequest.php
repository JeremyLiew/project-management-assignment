<?php
// Jeremy
namespace App\Http\Requests\Log;

use Illuminate\Foundation\Http\FormRequest;

class LogFilterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user' => 'nullable|string|max:255',
            'created_at' => 'nullable|date',
            'action' => 'nullable|string|in:Created,Updated,Deleted',
            'log_level' => 'nullable|string|in:INFO,DEBUG,ERROR,WARNING',
        ];
    }
}
