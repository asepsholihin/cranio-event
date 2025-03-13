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
        Schema::create('log_trip_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('umroh_trip_id');
            $table->string('log_type');
            $table->string('message');
            $table->timestamps();
        });
        Schema::create('log_product_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('product_id');
            $table->string('log_type');
            $table->string('message');
            $table->timestamps();
        });
        Schema::create('log_article_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('article_id');
            $table->string('log_type');
            $table->string('message');
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
        Schema::dropIfExists('log_trip_activities');
        Schema::dropIfExists('log_product_activities');
        Schema::dropIfExists('log_article_activities');
    }
};
