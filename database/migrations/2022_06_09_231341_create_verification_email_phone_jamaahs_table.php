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
        Schema::create('verification_email_phone_participants', function (Blueprint $table) {
            $table->id();
            $table->enum('via', ['email', 'phone']);
            $table->string('verify');
            $table->string('code', 20);
            $table->tinyInteger('sent_times')->default(0);
            $table->timestamps();
            $table->unique(['via','verify']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verification_email_phone_participants');
    }
};
