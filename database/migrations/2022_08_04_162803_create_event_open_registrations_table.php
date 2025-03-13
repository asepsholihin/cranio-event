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
        Schema::create('event_open_registrations', function (Blueprint $table) {
            $table->uuid();
            $table->tinyText('name');
            $table->tinyText('description');
            $table->string('location');
            $table->date('event_date');
            $table->boolean('close_registration')->default(false);
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
        Schema::dropIfExists('event_open_registrations');
    }
};
