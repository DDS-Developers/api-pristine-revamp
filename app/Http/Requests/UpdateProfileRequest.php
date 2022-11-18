<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;

class UpdateProfileRequest extends FormRequest
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
        $user = auth()->user();
        return [
            'username' => [
                'required',
                'unique:' . User::tableName . ',username,' . $user->id,
            ],
            'avatar' => ['required'],
            'fullname' => ['required'],
            'phone' => [
                'required',
                'unique:' . User::tableName . ',phone,' . $user->id,
            ],
            'email' => [
                'required',
                'unique:' . User::tableName . ',email,' . $user->id,
            ],
            'password' => ['required'],
            'confirm_password' => [
                'required_with:password_confirmation',
                'same:password'
            ]
        ];
    }
}
