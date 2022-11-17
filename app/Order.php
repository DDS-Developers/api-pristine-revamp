<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const tableName = 'orders';

    protected $table = self::tableName;

    protected $fillable = [
        'nama',
        'kota',
        'handphone',
        'galon',
        'refill_galon',
        '15lt',
        '600ml',
        '400ml',
        'email',
        'voucher_code',
        'consent',
        'nik',
        'postal_code',
        'alamat',
        'preferensi_bahasa',
        'code',
    ];

    public function voucher()
    {
        return $this->belongsTo(Promo::class, 'voucher_code', 'voucher_code');
    }
}
