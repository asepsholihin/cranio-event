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
        Schema::create('summary_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umroh_trip_id');
            $table->foreignId('category_id');
            $table->string('attendance_name')->nullable();
            $table->string('evidence')->nullable();
            $table->string('notes')->nullable();
            $table->integer('total_attendance')->default(0);
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
        Schema::dropIfExists('summary_attendances');
    }
};
