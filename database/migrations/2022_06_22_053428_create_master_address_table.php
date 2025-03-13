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
        Schema::create('master_address', function (Blueprint $table) {
            $table->id();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('postalcode')->nullable();
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
        Schema::dropIfExists('master_address');
    }
};
