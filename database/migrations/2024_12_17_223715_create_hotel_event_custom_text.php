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
        Schema::create('hotel_event_custom_text', function (Blueprint $table) {
            $table->id();
            $table->integer('master_hotel_event_id')->unsigned()->nullable();
            $table->enum('category', ['manasik_hd_price', 'manasik_fd_price', 'advantages', 'disadvantages'])->nullable();
            $table->text('custom_text')->nullable();
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
        Schema::dropIfExists('hotel_event_custom_text');
    }
};
