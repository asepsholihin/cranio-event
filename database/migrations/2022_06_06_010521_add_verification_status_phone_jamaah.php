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
            $table->tinyInteger('access_status')->default(1);
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('pin')->nullable();
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
            $table->dropColumn('access_status');
            $table->dropColumn('phone_verified_at');
            $table->dropColumn('pin');
        });
    }
};
