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
        Schema::table('event_attendances', function (Blueprint $table) {
            $table->integer('transit_hotel_id')->nullable();
            $table->time('event_end_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_attendances', function (Blueprint $table) {
            $table->dropColumn('transit_hotel_id');
            $table->dropColumn('event_end_at');
        });
    }
};
