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
        Schema::table('web_content_settings', function (Blueprint $table) {
            $table->string('icon_url')->nullable();
            $table->text('forms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_content_settings', function (Blueprint $table) {
            $table->dropColumn('icon_url');
            $table->dropColumn('forms');
        });
    }
};
