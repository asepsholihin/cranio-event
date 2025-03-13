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
            $table->string('background_ustadz_salim')->nullable();
            $table->string('background_package')->nullable();
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
            $table->dropColumn('background_ustadz_salim');
            $table->dropColumn('background_package');
        });
    }
};
