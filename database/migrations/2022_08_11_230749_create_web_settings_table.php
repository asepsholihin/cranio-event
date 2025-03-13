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
        Schema::create('web_settings', function (Blueprint $table) {
            $table->id();
            $table->string('web_logo')->nullable();
            $table->string('web_favicon')->nullable();
            $table->string('web_title')->nullable();
            $table->string('web_email')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->foreignId('lang_id')->nullable()->unsigned();
            $table->string('phone_number')->nullable();
            $table->string('wa_number_1')->nullable();
            $table->string('wa_number_2')->nullable();
            $table->string('copyright_text')->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('footer_consultation')->nullable();
            $table->string('footer_location')->nullable();
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
        Schema::dropIfExists('web_settings');
    }
};
