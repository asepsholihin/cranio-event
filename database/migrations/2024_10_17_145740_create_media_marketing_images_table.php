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
        Schema::create('media_marketing_images', function (Blueprint $table) {
            $table->id();
            $table->integer('media_marketing_id');
            $table->string('image_url')->nullable();
            $table->tinyInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::table('media_marketings', function (Blueprint $table) {
            $table->string('type')->default('banner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_marketing_images');
        
        Schema::table('media_marketings', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
