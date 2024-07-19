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
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->text('url')->nullable();
            $table->text('caption')->nullable();
            $table->text('target_post')->nullable();
            $table->enum('status', ['created', 'checked', 'approved', 'rejected', 'final_created', 'final_checked', 'final_approved', 'final_rejected'])->default('created');
            $table->timestamp('checked_by_date', $precision = 0)->nullable();
            $table->text('checked_by_email')->nullable();
            $table->text('checked_by_remarks')->nullable();
            $table->timestamp('final_checked_by_date', $precision = 0)->nullable();
            $table->text('final_checked_by_email')->nullable();
            $table->text('final_checked_by_remarks')->nullable();
            $table->timestamp('approved_by_date', $precision = 0)->nullable();
            $table->text('approved_by_email')->nullable();
            $table->text('approved_by_remarks')->nullable();
            $table->timestamp('final_approved_by_date', $precision = 0)->nullable();
            $table->text('final_approved_by_email')->nullable();
            $table->text('final_approved_by_remarks')->nullable();
            $table->timestamp('rejected_by_date', $precision = 0)->nullable();
            $table->text('rejected_by_email')->nullable();
            $table->text('rejected_by_remarks')->nullable();
            $table->timestamp('final_rejected_by_date', $precision = 0)->nullable();
            $table->text('final_rejected_by_email')->nullable();
            $table->text('final_rejected_by_remarks')->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->bigInteger('users_id')->unsigned()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::table('media', function (Blueprint $table) {
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
        Schema::dropIfExists('media');
    }
};
