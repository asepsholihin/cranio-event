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
            $table->boolean('view_in_report')->default(false);
            $table->string('title_in_report')->nullable();
            $table->string('model_in_report')->nullable();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->string('title_in_report')->nullable();
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
            $table->dropColumn('view_in_report');
            $table->dropColumn('title_in_report');
            $table->dropColumn('model_in_report');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('title_in_report');
        });
    }
};
