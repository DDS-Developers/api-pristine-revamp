<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BandungSubmissionToken extends Model
{
    protected $table = 'bandung_submission_tokens';
    protected $primaryKey = 'id';
    protected $fillable = ['token', 'is_used'];
}
