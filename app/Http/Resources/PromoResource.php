<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'promo_title' => $this->promo_title,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'voucher_code' => $this->voucher_code,
            'promo_thumbnail' => $this->promo_thumbnail,
            'promo_image' => $this->promo_image,
            'term_cond' => $this->term_cond,
            'galon' => $this->galon,
            'refill_galon' => $this->refill_galon,
            'botol_15lt' => $this->botol_15lt,
            'botol_600ml' => $this->botol_600ml,
            'botol_400ml' => $this->botol_400ml,
            'status' => $this->status,
            'max_redeem' => $this->max_redeem,
            'total_redeem' => $this->total_redeem,
        ];
    }
}
