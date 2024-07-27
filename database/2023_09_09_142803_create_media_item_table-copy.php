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
        Schema::create('media_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->text('attachment_images')->nullable();
            $table->text('attachment_videos')->nullable();
            $table->text('attachment_images_revision')->nullable();
            $table->text('attachment_videos_revision')->nullable();
            $table->text('caption')->nullable();
            $table->integer('value')->default(0);
            $table->enum('status', [
                'created',
                'final_created',
                'final_checked',
                'final_approved',
                'final_approved_2',
                'final_approved_3',
                'final_rejected',
                'final_rejected_2',
                'final_rejected_3'
                ])->default('created');
            $table->timestamp('final_checked_by_date', $precision = 0)->nullable();
            $table->text('final_checked_by_email')->nullable();
            $table->text('final_checked_by_remarks')->nullable();
            $table->timestamp('final_created_by_date', $precision = 0)->nullable();
            $table->text('final_created_by_email')->nullable();
            $table->text('final_created_by_remarks')->nullable();
            $table->timestamp('final_approved_by_date', $precision = 0)->nullable();
            $table->text('final_approved_by_email')->nullable();
            $table->text('final_approved_by_remarks')->nullable();
            $table->timestamp('final_approved_2_by_date', $precision = 0)->nullable();
            $table->text('final_approved_2_by_email')->nullable();
            $table->text('final_approved_2_by_remarks')->nullable();
            $table->timestamp('final_approved_3_by_date', $precision = 0)->nullable();
            $table->text('final_approved_3_by_email')->nullable();
            $table->text('final_approved_3_by_remarks')->nullable();
            $table->timestamp('final_rejected_by_date', $precision = 0)->nullable();
            $table->text('final_rejected_by_email')->nullable();
            $table->text('final_rejected_by_remarks')->nullable();
            $table->timestamp('final_rejected_2_by_date', $precision = 0)->nullable();
            $table->text('final_rejected_2_by_email')->nullable();
            $table->text('final_rejected_2_by_remarks')->nullable();
            $table->timestamp('final_rejected_3_by_date', $precision = 0)->nullable();
            $table->text('final_rejected_3_by_email')->nullable();
            $table->text('final_rejected_3_by_remarks')->nullable();
            $table->bigInteger('media_id')->unsigned()->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->bigInteger('users_id')->unsigned()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });

        Schema::table('media_item', function (Blueprint $table) {
            $table->foreign('media_id')->references('id')->on('media');
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
        Schema::dropIfExists('media_item');
    }
};
