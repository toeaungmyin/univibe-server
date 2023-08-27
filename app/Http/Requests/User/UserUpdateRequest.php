<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'username' => [
                'unique:users,username',
            ],
            'email' => [
                'email',
                Rule::unique('users', 'email')->ignore($this->user()->id),
                'ends_with:@ucsm.edu.mm'
            ],
            'password' => [
                'nullable',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
                // requires at least one lowercase letter
                // requires at least one uppercase letter
                // requires at least one special character from the specified symbols
                // matches a combination of letters, digits, and special characters with a minimum length of 8 characters.
            ],
            'profile_url' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
