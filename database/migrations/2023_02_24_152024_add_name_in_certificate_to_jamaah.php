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
            $table->string('name_in_certificate')->nullable();
            $table->integer('counter')->nullable();
            $table->string('participant_code')->nullable();
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
            $table->dropColumn('name_in_certificate');
            $table->dropColumn('counter');
            $table->dropColumn('participant_code');
        });
    }
};
