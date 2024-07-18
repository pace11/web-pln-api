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
        Schema::create('manage_link', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('key')->nullable();
            $table->text('title')->nullable();
            $table->text('period')->nullable();
            $table->text('url')->nullable();
            $table->boolean('active')->default(true);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
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
        Schema::dropIfExists('manage_link');
    }
};
