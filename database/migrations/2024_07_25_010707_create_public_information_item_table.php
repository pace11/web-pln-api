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
        Schema::create('public_information_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('period_date', $precision = 0)->nullable();
            $table->text('attachment')->nullable();
            $table->integer('value')->default(0);
            $table->bigInteger('public_information_id')->unsigned()->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->bigInteger('users_id')->unsigned()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::table('public_information_item', function (Blueprint $table) {
            $table->foreign('public_information_id')->references('id')->on('public_information');
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('unit_id')->references('id')->on('unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_information_item');
    }
};
