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
        Schema::table('participants', function (Blueprint $table) {
            $table->string('room_number')->nullable();
            $table->string('received_by')->nullable();
            $table->integer('given_by')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->string('room_key_evidence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('room_number');
            $table->dropColumn('received_by');
            $table->dropColumn('given_by');
            $table->dropColumn('received_at');
            $table->dropColumn('room_key_evidence');
        });
    }
};
