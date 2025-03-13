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
        Schema::create('faq_contents', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->integer("faq_category_id");
            $table->text("content");
            $table->tinyInteger("status");
            $table->foreignId("created_by");
            $table->foreignId("updated_by");
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
        Schema::dropIfExists('faq_contents');
    }
};
