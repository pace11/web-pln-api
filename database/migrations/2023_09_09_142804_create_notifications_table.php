<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('posts_id')->nullable();
            $table->enum('status', [
                'created',
                'checked',
                'approved',
                'rejected',
                'final_created',
                'final_checked',
                'final_approved',
                'final_approved_2',
                'final_approved_3',
                'final_rejected',
                'final_rejected_2',
                'final_rejected_3'
                ])->default('created');
            $table->bigInteger('users_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
