<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandungSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bandung_submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('nik');
            $table->string('email');
            $table->string('city');
            $table->string('postal_code');
            $table->text('address');
            $table->string('phone');
            $table->string('unique_code')->nullable();
            $table->boolean('has_checked_in')->default(false);
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
        Schema::dropIfExists('bandung_submissions');
    }
}
