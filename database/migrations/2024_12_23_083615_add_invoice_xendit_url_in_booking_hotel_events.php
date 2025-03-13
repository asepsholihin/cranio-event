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
            $table->integer('event_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('invoice_description')->nullable();
            $table->string('invoice_xendit_url')->nullable();
            $table->string('receipt_url')->nullable();
            $table->integer('additional_pax')->nullable();
            $table->json('assigned_participant')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->double('paid_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_hotel_events', function (Blueprint $table) {
            $table->dropColumn('event_id');
            $table->dropColumn('invoice_no');
            $table->dropColumn('invoice_description');
            $table->dropColumn('invoice_xendit_url');
            $table->dropColumn('receipt_url');
            $table->dropColumn('additional_pax');
            $table->dropColumn('assigned_participant');
            $table->dropColumn('paid_at');
            $table->dropColumn('paid_amount');
        });
    }
};
