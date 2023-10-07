<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'content' => 'required|string|',
            'audience_type' => 'required|string',
            'username' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'content không được để trống',
            'content.string' => 'content phải là dạng chuỗi',
            'audience_type.required' => 'audience_type không được để trống',
            'username.required' => 'username không được để trống'
        ];
    }
}
