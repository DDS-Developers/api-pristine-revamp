<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PristimePhotoAlbumContent extends Model
{
    protected $table = 'pristime_photo_album_contents';
    protected $guarded = [];

    public function pristimePhotoAlbum()
    {
        return $this->belongsTo(PristimePhotoAlbum::class, 'pristime_photo_album_id', 'id');
    }
}
