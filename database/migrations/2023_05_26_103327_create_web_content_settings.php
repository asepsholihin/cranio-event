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
        Schema::create('web_content_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('section')->nullable();
            $table->string('content_type');
            $table->string('image_url')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('text')->nullable();
            $table->string('link_url')->nullable();
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
        Schema::dropIfExists('web_content_settings');
    }
};
