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
        Schema::create('event_open_seats', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id');
            $table->integer('parent_account_id');
            $table->string('seat_name');
            $table->string('seat_number');
            $table->tinyInteger('gender');
            $table->string('barcode')->nullable();
            $table->string('barcode_thumbnail')->nullable();
            $table->timestamp('check_in_at')->nullable();
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
        Schema::dropIfExists('event_open_seats');
    }
};
