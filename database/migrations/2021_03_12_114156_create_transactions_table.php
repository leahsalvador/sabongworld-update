<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_from')->nullable();
            $table->foreignId('user_to');
            $table->text('details')->nullable();
            $table->double('amount',12,2);
            $table->enum('type',['wallet','comission'])->nullable();
            $table->enum('transaction_type',['deposit','withdraw','agent-withdraw','agent-deposit','system-withdraw','system-deposit'])->nullable();
            $table->enum('transaction_status',['pending','success','cancelled'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
