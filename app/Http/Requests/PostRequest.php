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

        // Luật xác thực chung cho cả tạo và cập nhật
        $commonRules = [
            'username' => 'required|string',
            'function' => 'required|string',
        ];

        if ($this->input('function') === 'C') {
            // Áp dụng luật xác thực riêng cho thêm mới
            return array_merge($commonRules, [
                'content' => 'required|string|',
                'audience_type' => 'required|string',
            ]);

        } elseif($this->input('function') === 'U') 
        {
            // Áp dụng luật xác thực riêng cho tạo mới
            return array_merge($commonRules, [
                // Luật xác thực cho tạo mới
                'id_post' => 'required|string',
                'content' => 'required|string|',
                'audience_type' => 'required|string',
                'media' => 'required|string'
            ]);
        } elseif($this->input('function') === 'D') 
        {
            // Áp dụng luật xác thực riêng cho tạo mới
            return array_merge($commonRules, [
                // Luật xác thực cho xóa
                'id_post' => 'required|string',
            ]);
        }

        return $commonRules;
    }

    public function messages()
    {
        return [
            'content.required' => 'content không được để trống',
            'audience_type.required' => 'audience_type không được để trống',
            'username.required' => 'username không được để trống',
            'id_post.required' => 'id_post không được để trống'
        ];
    }
}
