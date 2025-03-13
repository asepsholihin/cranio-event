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
        Schema::table('form_questions', function (Blueprint $table) {
            $table->tinyInteger('order')->nullable();
            $table->boolean('has_other')->default(false);
            $table->tinyInteger('scale_top')->nullable();
            $table->tinyInteger('scale_bottom')->nullable();
            $table->string('label_scale_top')->nullable();
            $table->string('label_scale_bottom')->nullable();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->boolean('required_umroh_trip')->default(false);
        });

        Schema::table('form_answers', function (Blueprint $table) {
            $table->integer('umroh_trip_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_questions', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('has_other');
            $table->dropColumn('scale_top');
            $table->dropColumn('scale_bottom');
            $table->dropColumn('label_scale_top');
            $table->dropColumn('label_scale_bottom');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('required_umroh_trip');
        });

        Schema::table('form_answers', function (Blueprint $table) {
            $table->dropColumn('umroh_trip_id');
        });
    }
};
