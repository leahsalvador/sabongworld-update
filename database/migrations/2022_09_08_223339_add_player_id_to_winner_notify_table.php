<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlayerIdToWinnerNotifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('winner_notify', function (Blueprint $table) {
            $table->renameColumn('round', 'game_rounds_id');
            $table->bigInteger('player_id')->index('player_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('winner_notify', function (Blueprint $table) {
            $table->dropColumn('player_id');
            $table->renameColumn('game_rounds_id', 'round');
        });
    }
}
