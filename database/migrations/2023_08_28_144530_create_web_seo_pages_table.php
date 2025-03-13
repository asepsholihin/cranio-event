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
        Schema::create('web_seo_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('web_blogs', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->string('canonical')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_seo_pages');

        Schema::table('web_blogs', function (Blueprint $table) {
            $table->dropColumn('meta_title');
            $table->dropColumn('canonical');
        });
    }
};
