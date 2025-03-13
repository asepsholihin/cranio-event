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
        Schema::table('web_seo_pages', function (Blueprint $table) {
            $table->string('og_type')->nullable();
            $table->string('og_url')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_site')->nullable();
            $table->string('twitter_card')->nullable();
            $table->string('twitter_title')->nullable();
            $table->string('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_seo_pages', function (Blueprint $table) {
            $table->dropColumn('og_type');
            $table->dropColumn('og_url');
            $table->dropColumn('og_title');
            $table->dropColumn('og_description');
            $table->dropColumn('og_image');
            $table->dropColumn('twitter_site');
            $table->dropColumn('twitter_card');
            $table->dropColumn('twitter_title');
            $table->dropColumn('twitter_description');
            $table->dropColumn('twitter_image');
        });
    }
};
