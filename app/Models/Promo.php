<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Enums\PromoStatusEnum;

class Promo extends Model
{
    private $column;


    protected $table = 'promo_voucher';

    protected $fillable = [
        'promo_title',
        'start_date',
        'end_date',
        'voucher_code',
        'promo_thumbnail',
        'promo_image',
        'terms_cond',
        'galon',
        'refill_galon',
        'botol_15lt',
        'botol_600ml',
        'botol_400ml',
        'status',
        'max_redeem',
        'total_redeem'
    ];

    public function getColumn()
    {
        $table = $this->getTable();
        return Schema::getColumnListing($table);
    }


    public function scopeOrder($q, $request)
    {
        $orderBy = $request->input('order_by', 'created_at');
        $sort = $request->input('sort', 'desc');
        if (in_array($orderBy, $this->getColumn()) && isset($orderBy) && isset($sort)) {
            $q->orderBy($orderBy, $sort);
        }
    }

    public function scopeSearch($q, $request)
    {
        $field = $request->input('searchBy');
        $keyword = $request->input('keyword');
        if (in_array($field, $this->getColumn()) && isset($field) && isset($keyword)) {
            $q->where($field, $keyword);
        }
    }

    public function scopeReturnType($q, $request)
    {
        if ($request->has('all') && $request->all == true) {
            return $q->get();
        }
        return $q->paginate($request->input('per_page', 10));
    }

    public function getPromoThumbnailAttribute($v)
    {
        if ($v !== NULL) {
            $v = asset($v);
        }
        return $v;
    }
    public function getPromoImagesAttribute($v)
    {
        if ($v !== NULL) {
            $v = asset($v);
        }
        return $v;
    }
}
