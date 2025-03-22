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
        Schema::create('temp_bookings', function (Blueprint $table) {
            $table->uuid();
            $table->string('account_name')->nullable();
            $table->string('account_email')->nullable();
            $table->string('account_wa')->nullable();
            $table->string('account_hospital')->nullable();
            $table->integer('total_pax')->nullable();
            $table->double('price_per_pax')->nullable();
            $table->double('total_price')->nullable();
            $table->string('package')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('temp_bookings');
    }
};
