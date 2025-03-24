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
        Schema::table('bookings', function (Blueprint $table) {
            $table->double('tax_amount')->nullable();
            $table->double('total_price_with_tax')->nullable();
            $table->string('room_number')->nullable();
            $table->string('received_by')->nullable();
            $table->foreignId('given_by')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->string('room_key_evidence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('tax_amount');
            $table->dropColumn('total_price_with_tax');
            $table->dropColumn('room_number');
            $table->dropColumn('received_by');
            $table->dropColumn('given_by');
            $table->dropColumn('received_at');
            $table->dropColumn('room_key_evidence');
        });
    }
};
