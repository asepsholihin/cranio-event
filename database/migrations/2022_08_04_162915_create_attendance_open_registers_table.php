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
        Schema::create('attendance_open_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_open_registration_id');
            $table->uuid('barcode');
            $table->string('name');
            $table->string('no_hp');
            $table->string('email')->nullable();
            $table->boolean('is_alumni')->default(false);
            $table->timestamp('check_in_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('attendance_open_registrations');
    }
};
