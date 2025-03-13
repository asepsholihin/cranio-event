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
        Schema::table('participant', function (Blueprint $table) {
            $table->boolean('wedding_book_required')->default(false);
            $table->double('chest_size')->nullable();
            $table->double('body_height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participant', function (Blueprint $table) {
            $table->dropColumn('wedding_book_required');
            $table->dropColumn('chest_size');
            $table->dropColumn('body_height');
        });
    }
};
