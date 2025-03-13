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
        Schema::create('log_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('summary_attendance_id');
            $table->foreignId('umroh_trip_id');
            $table->foreignId('category_id');
            $table->foreignId('participant_id');
            $table->string('received_evidence')->nullable();
            $table->tinyInteger('total_bags')->default(0);
            $table->tinyInteger('attendance_status')->default(0);
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
        Schema::dropIfExists('log_attendances');
    }
};
