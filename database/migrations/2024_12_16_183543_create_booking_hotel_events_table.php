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
        Schema::create('booking_hotel_events', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id')->nullable();
            $table->string('hotel_name')->nullable();
            $table->integer('participant_id');
            $table->integer('umroh_trip_id');
            $table->integer('order_umroh_trip_id');
            $table->integer('package_umroh_trip_id');
            $table->string('phone_number')->nullable();
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->string('room_type')->nullable();
            $table->double('room_price_pax')->nullable();
            $table->integer('total_room')->nullable();
            $table->integer('total_pax')->nullable();
            $table->string('additional_item')->nullable();
            $table->double('additional_cost')->nullable();
            $table->double('total_amount')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->default("pending"); //'pending', 'paid', 'booked', 'accessgiven', 'cancelled'
            $table->string('room_number')->nullable();
            $table->string('access_evidence')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_hotel_events');
    }
};
