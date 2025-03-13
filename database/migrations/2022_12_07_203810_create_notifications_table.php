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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('notification_type');
            $table->integer('department_id');
            $table->integer('order_umroh_trip_id');
            $table->text('message');
            $table->string('page_url');
            $table->integer('created_by');
            $table->timestamps();
        });

        Schema::create('notification_read_log', function (Blueprint $table) {
            $table->id();
            $table->integer('notification_id');
            $table->integer('read_by');
            $table->dateTime('read_at');
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
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_read_log');
    }
};
