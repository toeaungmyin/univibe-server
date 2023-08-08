<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'audience' => 'required|string|in:public,private,friends', // 'audience' should be one of these values.
            'content' => 'nullable|string|max:1000', // Increase the max length to 1000 characters.
            'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048', // Allow only specific image formats and reduce the max size to 2MB (2048 KB).
        ];

    }
}
