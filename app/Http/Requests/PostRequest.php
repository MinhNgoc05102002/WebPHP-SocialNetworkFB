<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'content' => 'required|string|max:10',
            'username' => 'required|string|max:10',
        ];
    }

    public function messages()
    {
        return [
            'content.max' => 'content is max la 123',
            'username.max' => 'user is max la 123',
            'content.required' => 'content is required 123',
            'username.required' => 'username is required 123'
        ];
    }
}
