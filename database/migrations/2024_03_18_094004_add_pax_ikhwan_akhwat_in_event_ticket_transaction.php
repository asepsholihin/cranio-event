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
        Schema::table('event_ticket_transactions', function (Blueprint $table) {
            $table->smallInteger('pax_ikhwan')->default(0);
            $table->smallInteger('pax_akhwat')->default(0);
        });

        Schema::table('attendance_open_registrations', function (Blueprint $table) {
            $table->string('last_umroh_trip')->nullable();
            $table->text('notes')->nullable();
            $table->smallInteger('pax_ikhwan')->default(0);
            $table->smallInteger('pax_akhwat')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_ticket_transactions', function (Blueprint $table) {
            $table->dropColumn('pax_ikhwan');
            $table->dropColumn('pax_akhwat');
        });

        Schema::table('attendance_open_registrations', function (Blueprint $table) {
            $table->dropColumn('last_umroh_trip');
            $table->dropColumn('notes');
            $table->dropColumn('pax_ikhwan');
            $table->dropColumn('pax_akhwat');
        });
    }
};
