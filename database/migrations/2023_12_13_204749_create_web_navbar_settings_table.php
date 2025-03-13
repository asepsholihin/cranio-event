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
        Schema::create('web_navbar_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->integer('order');
            $table->tinyInteger('parent_id')->nullable();
            $table->tinyInteger('show');
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('web_navbar_settings');
    }
};
