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
        Schema::create('web_link_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lang_id')->nullable()->unsigned();
            $table->integer('page_id');
            $table->string('page_name');
            $table->text('whatsapp_text');
            $table->text('google_tag');
            $table->smallInteger('status');
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
        Schema::dropIfExists('web_link_texts');
    }
};
