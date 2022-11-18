<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;

class LoginRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'username' => [
                'required',
                function ($attribute, $val, $fail) {
                    $user = User::where('email', $val)
                        ->orWhere('phone', $val)
                        ->orWhere('username', $val)
                        ->exists();

                    if (!$user) {
                        $fail('Username Not Found');
                    }
                },
            ],
            'password' => [
                'required'
            ]
        ];
    }
}
