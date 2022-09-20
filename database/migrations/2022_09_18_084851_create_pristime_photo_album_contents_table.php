<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePristimePhotoAlbumContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pristime_photo_album_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pristime_photo_album_id');
            $table->text('file_path');
            $table->text('clean_file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pristime_photo_album_contents');
    }
}
