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
        Schema::create('web_about_settings', function (Blueprint $table) {
            $table->id();
            $table->string('header_image')->nullable();
            $table->string('header_title')->nullable();
            $table->string('vision_title')->nullable();
            $table->string('vision_text_1')->nullable();
            $table->string('vision_image_1')->nullable();
            $table->string('vision_text_2')->nullable();
            $table->string('vision_image_2')->nullable();
            $table->string('vision_text_3')->nullable();
            $table->string('vision_image_3')->nullable();
            $table->string('legality_title')->nullable();
            $table->string('legality_image')->nullable();
            $table->string('executive_title')->nullable();
            $table->string('executive_image')->nullable();
            $table->string('director_title')->nullable();
            $table->string('director_image')->nullable();
            $table->text('director_text')->nullable();
            $table->string('org_title')->nullable();
            $table->string('org_image')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('web_about_settings');
    }
};
