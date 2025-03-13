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
        Schema::create('hotel_rate_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id');
            $table->date('transaction_date')->nullable();
            $table->integer('room_single_rate')->nullable();
            $table->integer('room_double_rate')->nullable();
            $table->integer('room_triple_rate')->nullable();
            $table->integer('room_quad_rate')->nullable();
            $table->integer('room_queen_rate')->nullable();
            $table->double('total_price')->nullable();
            $table->string('currency')->nullable();
            $table->double('price_convertion')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_rate_histories');
    }
};
