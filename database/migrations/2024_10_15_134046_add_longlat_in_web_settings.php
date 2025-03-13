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
        Schema::table('web_settings', function (Blueprint $table) {
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('title_maps')->nullable();
            $table->string('siskopatuhimg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('title_maps');
            $table->dropColumn('siskopatuhimg');
        });
    }
};
