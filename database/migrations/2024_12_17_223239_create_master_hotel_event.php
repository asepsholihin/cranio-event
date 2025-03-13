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
        Schema::create('master_hotel_event', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_name')->nullable();
            $table->integer('hotel_city')->unsigned()->nullable();
            $table->enum('is_manasik', ['yes', 'no'])->nullable()->default('no');
            $table->enum('is_transit', ['yes', 'no'])->nullable()->default('no');
            $table->string('hotel_pic')->nullable();
            $table->string('hotel_pic_number')->nullable();
            $table->string('hotel_map_url', 500)->nullable();
            $table->text('hotel_address')->nullable();
            $table->double('manasik_hd_price')->nullable();
            $table->double('manasik_fd_price')->nullable();
            $table->enum('prefer_for_manasik', ['yes', 'no'])->nullable()->default('no');
            $table->enum('prefer_for_transit', ['yes', 'no'])->nullable()->default('no');
            $table->integer('manasik_counter')->nullable();
            $table->integer('transit_counter')->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable()->default('active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('master_hotel_event');
    }
};
