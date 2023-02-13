<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionArchiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_archive', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('round');
            $table->bigInteger('from')->index();
            $table->bigInteger('to')->index();
            $table->float('amount');
            $table->float('commission_percentage');
            $table->string('details')->nullable();
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
        Schema::dropIfExists('commission_archive');
    }
}
