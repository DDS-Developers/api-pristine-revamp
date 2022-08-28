<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'district';
    protected $guarded  = [];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function subDistrict()
    {
        return $this->hasMany(District::class, 'district_id', 'id');
    }
}
