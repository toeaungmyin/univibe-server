<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => [
                'required',
                'max:50'
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'ends_with:@ucsm.edu.mm'
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
                // requires at least one lowercase letter
                // requires at least one uppercase letter
                // requires at least one special character from the specified symbols
                // matches a combination of letters, digits, and special characters with a minimum length of 8 characters.
            ]
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one special character (@$!%*#?&), and be at least 8 characters long.',
        ];
    }
}
