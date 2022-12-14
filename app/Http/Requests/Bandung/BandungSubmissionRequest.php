<?php

namespace App\Http\Requests\Bandung;

use Illuminate\Foundation\Http\FormRequest;

class BandungSubmissionRequest extends FormRequest
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
            'name' => 'required',
            'nik' => 'required|unique:bandung_submissions|numeric',
            'email' => 'required|unique:bandung_submissions|email',
            'city' => 'required',
            'postal_code' => 'required',
            'address' => 'required',
            'phone' => 'required|unique:bandung_submissions',
            'token' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Silakan isi nama anda.',
            'nik.required' => 'Silakan isi NIK anda.',
            'nik.unique' => 'NIK yang sama sudah terdaftar.',
            'email.required' => 'Silakan isi email anda.',
            'email.email' => 'Silakan isi email yang valid.',
            'email.unique' => 'Email yang sama sudah terdaftar.',
            'city.required' => 'Silakan isi kota anda.',
            'postal_code.required' => 'Silakan isi kode pos anda.',
            'address.required' => 'Silakan isi alamat anda.',
            'phone.required' => 'Silakan isi nomor handphone anda.',
            'phone.unique' => 'Nomor handphone yang sama sudah terdaftar.',
            'token.required' => 'Silakan isi token anda.',
        ];
    }
}
