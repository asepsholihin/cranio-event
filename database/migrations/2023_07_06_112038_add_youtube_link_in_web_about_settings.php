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
        Schema::table('web_about_settings', function (Blueprint $table) {
            $table->string('youtube_link')->nullable();
            $table->string('text_inquiry')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_about_settings', function (Blueprint $table) {
            $table->dropColumn('youtube_link');
            $table->dropColumn('text_inquiry');
        });
    }
};
