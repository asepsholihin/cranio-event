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
        Schema::table('booking_hotel_events', function (Blueprint $table) {
            $table->integer('umroh_trip_id')->change()->nullable();
            $table->integer('order_umroh_trip_id')->change()->nullable();
            $table->integer('package_umroh_trip_id')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
