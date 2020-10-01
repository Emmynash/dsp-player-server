<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDsaSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dsa_songs', function (Blueprint $table) {
            $table->id();
            $table->string('dsa_album_id');
            $table->string('dsa_song_id');
            $table->string('spotify_id')->nullable();
            $table->string('apple_id')->nullable();
            $table->string('nugs_id');
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
        Schema::dropIfExists('dsa_songs');
    }
}
