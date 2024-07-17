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
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('slug')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('thumbnail')->nullable();
            $table->boolean('posted')->nullable();
            $table->boolean('banner')->nullable();
            $table->enum('status', ['created', 'checked', 'approved', 'rejected', 'final_checked', 'final_approved', 'final_rejected'])->default('created');
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
            $table->integer('categories_id')->unsigned()->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->bigInteger('users_id')->unsigned()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('categories_id')->references('id')->on('categories');
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
        Schema::dropIfExists('posts');
    }
};
