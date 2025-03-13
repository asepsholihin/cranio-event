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
        Schema::create('web_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lang_id')->nullable()->unsigned();
            $table->string('image_url');
            $table->integer('order');
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
        Schema::dropIfExists('web_partners');
    }
};
