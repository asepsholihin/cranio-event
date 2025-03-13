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
        Schema::create('web_blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lang_id')->nullable()->unsigned();
            $table->integer('category_id')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('keywords')->nullable();
            $table->text('content')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('order')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_blogs');
    }
};
