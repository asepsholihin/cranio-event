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
        Schema::table('event_open_registrations', function (Blueprint $table) {
            $table->time('event_at')->nullable();
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
        Schema::table('event_open_registrations', function (Blueprint $table) {
            $table->dropColumn('event_at');
            $table->dropColumn('slug');
        });
    }
};
