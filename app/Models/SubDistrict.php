<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubDistrict extends Model
{
    protected $table = 'sub_district';
    protected $guarded  = [];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
}
