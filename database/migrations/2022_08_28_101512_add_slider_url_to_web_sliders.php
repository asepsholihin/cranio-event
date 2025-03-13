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
        Schema::table('web_sliders', function (Blueprint $table) {
            $table->string('slider_url')->nullable();
            $table->dropColumn('deleted');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_sliders', function (Blueprint $table) {
            $table->dropColumn('slider_url');
            $table->tinyInteger('deleted')->default(0);
            $table->dropSoftDeletes();
        });
    }
};
