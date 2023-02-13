<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWinnerArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winner_archive', function (Blueprint $table) {
            $table->id();
            $table->integer('game_rounds_id')->index('game_rounds_id');
            $table->string('winner');
            $table->float('amount');
            $table->bigInteger('player_id')->index('player_id');
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
        Schema::dropIfExists('winner_archive');
    }
}
