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
        Schema::table('web_content_settings', function (Blueprint $table) {
            $table->string('title_color')->nullable();
            $table->string('subtitle_color')->nullable();
            $table->string('text_color')->nullable();
        });

        Schema::table('web_sliders', function (Blueprint $table) {
            $table->string('title_color')->nullable();
            $table->string('description_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_content_settings', function (Blueprint $table) {
            $table->dropColumn('title_color');
            $table->dropColumn('subtitle_color');
            $table->dropColumn('text_color');
        });

        Schema::table('web_sliders', function (Blueprint $table) {
            $table->dropColumn('title_color');
            $table->dropColumn('description_color');
        });
    }
};
