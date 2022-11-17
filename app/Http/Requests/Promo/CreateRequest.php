<?php

namespace App\Http\Requests\Promo;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PromoStatusEnum;
use App\Models\Promo;

class CreateRequest extends FormRequest
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
        $rules =  [
            'promo_title' => 'required|unique:' . Promo::tableName . ',promo_title',
            'max_redeem' => 'nullable|numeric',
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d',
            'voucher_code' => 'required',
            'status' => 'in:' . implode(',', (new PromoStatusEnum)->getListStatus()),
            'term_cond' => 'required',
            'galon' => 'required|integer',
            'refill_galon' => 'required|integer',
            'botol_15lt' => 'required|integer',
            'botol_600ml' => 'required|integer',
            'botol_400ml' => 'required|integer',
            'promo_thumbnail' => 'required|file',
            'promo_image' => 'required|file',
        ];

        if (isset($this->promo)) {
            unset($rules['promo_thumbnail']);
            unset($rules['promo_image']);
            $rules['promo_title'] = $rules['promo_title'] . ',' . $this->promo->id;
        }


        return $rules;
    }

    public function messages()
    {

        return [];
    }
}
