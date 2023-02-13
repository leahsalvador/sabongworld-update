<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBettingArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('betting_archive', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_rounds_id');
            $table->foreignId('player_id');
            $table->decimal('amount', '12', '2')->default(0);
            $table->decimal('win_amount', '12', '2')->default(0);
            $table->decimal('loose_amount', '12', '2')->default(0);
            $table->decimal('current_points', '12', '2')->default(0);
            $table->integer('coin1')->default(0);
            $table->integer('coin2')->default(0);
            $table->enum('side', ['heads', 'tails']);
            $table->enum('status', ['win', 'loose', 'ongoing', 'cancelled', 'draw', 'undo']);
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
        Schema::dropIfExists('betting_archive');
    }
}
