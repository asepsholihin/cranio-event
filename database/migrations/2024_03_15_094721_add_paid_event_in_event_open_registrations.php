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
            $table->time('event_end_at')->nullable();
            $table->string('speaker')->nullable();
            $table->tinyInteger('is_paid_event')->default(0);
            $table->integer('number_of_seats')->nullable();
            $table->double('price')->nullable();
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
            $table->dropColumn('event_end_at');
            $table->dropColumn('speaker');
            $table->dropColumn('is_paid_event');
            $table->dropColumn('number_of_seats');
            $table->dropColumn('price');
        });
    }
};
