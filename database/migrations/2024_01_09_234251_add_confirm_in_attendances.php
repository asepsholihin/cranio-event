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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('confirm')->nullable();
            $table->timestamp('confirm_at')->nullable();
        });
        Schema::table('event_attendances', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('confirm');
            $table->dropColumn('confirm_at');
        });
        Schema::table('event_attendances', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
