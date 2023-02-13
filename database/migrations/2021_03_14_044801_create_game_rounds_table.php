<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_rounds', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_bet','12','2')->default(0);
            $table->decimal('total_bet_heads','12','2')->default(0);
            $table->decimal('total_bet_tails','12','2')->default(0);
            $table->decimal('head_payout','12','2')->default(0);
            $table->decimal('tails_payout','12','2')->default(0);
            $table->integer('round')->default(1);
            $table->integer('coin1')->default(0);
            $table->integer('coin2')->default(0);
            $table->string('wala');
            $table->string('meron');
            $table->string('Game_color');
            $table->enum('winner',['heads','tails','none','draw'])->default('none');
            $table->enum('status',['upcoming','open','closed','done','cancelled','final-bet']);
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
        Schema::dropIfExists('game_rounds');
    }
}
