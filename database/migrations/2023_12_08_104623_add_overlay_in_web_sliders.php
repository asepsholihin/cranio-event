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
        Schema::table('web_sliders', function (Blueprint $table) {
            $table->tinyInteger('overlay')->default(1);
        });
        Schema::table('web_content_settings', function (Blueprint $table) {
            $table->tinyInteger('overlay')->default(0);
        });
        Schema::table('web_about_settings', function (Blueprint $table) {
            $table->tinyInteger('header_overlay')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_sliders', function (Blueprint $table) {
            $table->dropColumn('overlay');
        });
        Schema::table('web_content_settings', function (Blueprint $table) {
            $table->dropColumn('overlay');
        });
        Schema::table('web_about_settings', function (Blueprint $table) {
            $table->dropColumn('header_overlay');
        });
    }
};
