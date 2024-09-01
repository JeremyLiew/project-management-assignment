<?php
// Jeremy
namespace App\Http\Requests\AboutUs;

use Illuminate\Foundation\Http\FormRequest;

class GetMembersRequest extends FormRequest
{
    public function rules()
    {
        return [
            'query' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'query.string' => 'The search query must be a string.',
            'query.max' => 'The search query may not be greater than 255 characters.',
        ];
    }
}
