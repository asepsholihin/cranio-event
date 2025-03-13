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
        Schema::create('web_index_settings', function (Blueprint $table) {
            $table->id();
            $table->string('why_us_title')->nullable();
            $table->string('about_image')->nullable();
            $table->string('about_title')->nullable();
            $table->string('profile_ustadz_image')->nullable();
            $table->string('profile_ustadz_title')->nullable();
            $table->string('tour_package_title')->nullable();
            $table->string('article_title')->nullable();
            $table->string('partner_title')->nullable();
            $table->foreignId('lang_id')->nullable()->unsigned();
            $table->text('video_url')->nullable();
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
        Schema::dropIfExists('web_index_settings');
    }
};
