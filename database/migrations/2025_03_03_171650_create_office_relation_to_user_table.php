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
        Schema::create('master_office_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('office_id');
            $table->timestamps();
        });

        Schema::create('department_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('department_id');
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
        Schema::dropIfExists('master_office_user');
        Schema::dropIfExists('department_user');
    }
};
