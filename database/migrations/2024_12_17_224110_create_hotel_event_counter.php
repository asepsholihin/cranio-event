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
        Schema::create('hotel_event_counter', function (Blueprint $table) {
            $table->id();
            $table->integer('master_hotel_event_id')->unsigned()->nullable();
            $table->integer('umroh_trip_id')->nullable();
            $table->integer('event_attendance_id')->nullable();
            $table->enum('purpose', ['manasik', 'transit'])->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_event_counter');
    }
};
