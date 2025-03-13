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
        Schema::create('media_marketings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('ratio_type')->default('square')->comment('square,portrait,landscape');
            $table->tinyInteger('layout_type')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('media_marketings');
    }
};
