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
        Schema::table('attendances', function (Blueprint $table) {
            $table->timestamp('check_in_at_online')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('at_online_in_attendances', function (Blueprint $table) {
            $table->dropColumn('check_in_at_online');
        });
    }
};
