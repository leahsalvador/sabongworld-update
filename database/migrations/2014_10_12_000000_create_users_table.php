<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->bigInteger('phone_number')->unique();
            $table->string('facebook_link')->nullable();;
            $table->string('code')->unique()->nullable();
            $table->string('referral_id')->nullable();
            $table->enum('user_level', ['admin', 'master-agent','master-agent-player','sub-agent-player','sub-agent']);
            $table->enum('status', ['activated', 'deactivated'])->defalt('activated');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
