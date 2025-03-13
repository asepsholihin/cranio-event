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
        Schema::table('web_link_texts', function (Blueprint $table) {
            $table->dropColumn('google_tag');
            $table->string('google_tag_event')->nullable();
            $table->string('google_tag_event_category')->nullable();
            $table->string('google_tag_event_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_link_texts', function (Blueprint $table) {
            $table->text('google_tag')->nullable();
            $table->dropColumn('google_tag_event');
            $table->dropColumn('google_tag_event_category');
            $table->dropColumn('google_tag_event_label');
        });
    }
};
