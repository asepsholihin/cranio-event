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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('temp_booking_id');
            $table->string('booking_no');
            $table->string('account_name')->nullable();
            $table->string('account_email')->nullable();
            $table->string('account_wa')->nullable();
            $table->string('account_hospital')->nullable();
            $table->integer('total_pax')->nullable();
            $table->integer('pax_assign')->nullable();
            $table->string('package')->nullable();
            $table->double('price_per_pax')->nullable();
            $table->double('total_price')->nullable();
            $table->double('total_paid')->nullable();
            $table->double('total_unpaid')->nullable();
            $table->string('order_status')->default("pending"); //'pending', 'paid', 'booked', 'accessgiven', 'cancelled'
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
        Schema::dropIfExists('bookings');
    }
};
