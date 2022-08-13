<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BandungSubmission extends Model
{
    protected $table = 'bandung_submissions';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'nik', 'email', 'postal_code', 'address', 'phone', 'voucher_code'];
}
