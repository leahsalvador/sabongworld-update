<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransactionPointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tid')->index();
            $table->bigInteger('user_id')->index();
            $table->float('points', 15, 2);
            $table->timestamps();

            $table->unique(['tid', 'user_id', 'points']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_history');
    }
}
