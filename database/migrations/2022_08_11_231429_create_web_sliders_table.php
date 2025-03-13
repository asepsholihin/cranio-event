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
        Schema::create('web_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->string('title');
            $table->string('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
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
        Schema::dropIfExists('web_sliders');
    }
};
