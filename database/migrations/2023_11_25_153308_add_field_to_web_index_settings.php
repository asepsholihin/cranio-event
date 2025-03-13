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
        Schema::table('web_index_settings', function (Blueprint $table) {
            $table->string('profile_ustadz_link', 255)->nullable();
            $table->string('about_link', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_index_settings', function (Blueprint $table) {
            $table->dropColumn('profile_ustadz_link');
            $table->dropColumn('about_link');
        });
    }
};
