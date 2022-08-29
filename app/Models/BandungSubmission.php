<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BandungSubmission extends Model
{
    protected $table = 'bandung_submissions';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'nik', 'email', 'city', 'postal_code', 'address', 'phone', 'unique_code'];
    protected $hidden = ['nik', 'phone'];
}
