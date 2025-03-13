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
        Schema::create('master_cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('master_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id');
            $table->string('name');
            $table->double('star');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->json('informations')->nullable();
            $table->json('images')->nullable();
            $table->string('map_url')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
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
        Schema::dropIfExists('master_cities');
        Schema::dropIfExists('master_hotels');
    }
};
