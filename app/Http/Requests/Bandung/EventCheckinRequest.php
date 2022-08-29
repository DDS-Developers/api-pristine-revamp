<?php

namespace App\Http\Requests\Bandung;

use Illuminate\Foundation\Http\FormRequest;

class EventCheckinRequest extends FormRequest
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
            'unique_code' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'unique_code.required' => 'Unique Code diperlukan.',
        ];
    }
}
