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
        Schema::table('web_blogs', function (Blueprint $table) {
            $table->string('show_in_page')->default('article');
            $table->string('type')->nullable();
            $table->string('youtube_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_blogs', function (Blueprint $table) {
            $table->dropColumn('show_in_page');
            $table->dropColumn('type');
            $table->dropColumn('youtube_link');
        });
    }
};
