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
            $table->string('image_thumbnail_url')->nullable();
        });

        Schema::table('event_ticket_transactions', function (Blueprint $table) {
            $table->string('last_umroh_trip')->nullable();
            $table->text('notes')->nullable();
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
            $table->dropColumn('image_thumbnail_url');
        });

        Schema::table('event_ticket_transactions', function (Blueprint $table) {
            $table->dropColumn('last_umroh_trip');
            $table->dropColumn('notes');
        });
    }
};
