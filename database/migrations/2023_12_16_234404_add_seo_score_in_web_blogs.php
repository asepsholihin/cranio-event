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
        Schema::table('web_blogs', function (Blueprint $table) {
            $table->integer('seo_score')->nullable();
            $table->json('seo_checks')->nullable();
            $table->tinyInteger('count_internal_link')->nullable();
            $table->tinyInteger('count_external_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_blogs', function (Blueprint $table) {
            $table->dropColumn('seo_score');
            $table->dropColumn('seo_checks');
            $table->dropColumn('count_external_link');
            $table->dropColumn('count_internal_link');
        });
    }
};
