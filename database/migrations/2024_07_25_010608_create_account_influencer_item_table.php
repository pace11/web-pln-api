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
        Schema::create('account_influencer_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('period_date', $precision = 0)->nullable();
            $table->text('attachment')->nullable();
            $table->integer('realization')->default(0);
            $table->integer('value')->default(0);
            $table->bigInteger('account_influencer_id')->unsigned()->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->bigInteger('users_id')->unsigned()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::table('account_influencer_item', function (Blueprint $table) {
            $table->foreign('account_influencer_id')->references('id')->on('account_influencer');
            $table->foreign('unit_id')->references('id')->on('unit');
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
        Schema::dropIfExists('account_influencer_item');
    }
};
