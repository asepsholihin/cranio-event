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
        Schema::create('participant', function (Blueprint $table) { // jama'ah is plural noun already
            $table->id();
            $table->string('name');
            $table->tinyInteger('gender');
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('no_passport', 20)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->tinyInteger('created_from');
            $table->timestamps();
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participant');
    }
};
