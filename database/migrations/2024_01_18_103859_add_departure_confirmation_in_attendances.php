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
            $table->integer('package_umroh_trip_id')->nullable();
            $table->string('group_bus')->nullable();
            $table->string('departure_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('package_umroh_trip_id');
            $table->dropColumn('group_bus');
            $table->dropColumn('departure_from');
        });
    }
};
