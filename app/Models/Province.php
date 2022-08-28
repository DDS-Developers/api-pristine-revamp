<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'province';
    protected $guarded  = [];

    public function city()
    {
        return $this->hasMany(City::class, 'province_id', 'id');
    }
}
