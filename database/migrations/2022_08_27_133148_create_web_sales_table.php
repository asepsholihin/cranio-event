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
        Schema::create('web_sales', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('lang_id')->default(1);
            $table->string('sales_name')->nullable();
            $table->integer('order_number')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_api')->nullable();
            $table->integer('total_visit')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_sales');
    }
};
