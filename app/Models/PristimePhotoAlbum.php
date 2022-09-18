<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PristimePhotoAlbum extends Model
{
    protected $table = 'pristime_photo_albums';
    protected $guarded = [];

    public function pristimePhotoAlbumContents()
    {
        return $this->hasMany(PristimePhotoAlbumContent::class, 'pristime_photo_album_id', 'id');
    }
}
