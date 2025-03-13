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
            $table->string('alt_image')->nullable();
        });

        Schema::table('web_index_settings', function (Blueprint $table) {
            $table->string('about_image_alt')->nullable();
            $table->string('profile_ustadz_image_alt')->nullable();
            $table->string('background_ustadz_salim_image_alt')->nullable();
            $table->string('background_package_image_alt')->nullable();
        });

        Schema::table('web_about_settings', function (Blueprint $table) {
            $table->string('header_image_alt')->nullable();
            $table->string('vision_1_image_alt')->nullable();
            $table->string('vision_2_image_alt')->nullable();
            $table->string('vision_3_image_alt')->nullable();
            $table->string('legality_image_alt')->nullable();
            $table->string('executive_image_alt')->nullable();
            $table->string('director_image_alt')->nullable();
            $table->string('org_image_alt')->nullable();
        });
        
        Schema::table('web_sliders', function (Blueprint $table) {
            $table->string('alt_image')->nullable();
        });

        Schema::table('web_whyus', function (Blueprint $table) {
            $table->string('alt_image')->nullable();
        });

        Schema::table('web_programs', function (Blueprint $table) {
            $table->string('alt_image')->nullable();
        });

        Schema::table('web_partners', function (Blueprint $table) {
            $table->string('alt_image')->nullable();
        });

        Schema::table('web_footer_logos', function (Blueprint $table) {
            $table->string('alt_image')->nullable();
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
            $table->dropColumn('alt_image');
        });

        Schema::table('web_index_settings', function (Blueprint $table) {
            $table->dropColumn('about_image_alt');
            $table->dropColumn('profile_ustadz_image_alt');
            $table->dropColumn('background_ustadz_salim_image_alt');
            $table->dropColumn('background_package_image_alt');
        });

        Schema::table('web_about_settings', function (Blueprint $table) {
            $table->dropColumn('header_image_alt');
            $table->dropColumn('vision_1_image_alt');
            $table->dropColumn('vision_2_image_alt');
            $table->dropColumn('vision_3_image_alt');
            $table->dropColumn('legality_image_alt');
            $table->dropColumn('executive_image_alt');
            $table->dropColumn('director_image_alt');
            $table->dropColumn('org_image_alt');
        });

        Schema::table('web_sliders', function (Blueprint $table) {
            $table->dropColumn('alt_image');
        });

        Schema::table('web_whyus', function (Blueprint $table) {
            $table->dropColumn('alt_image');
        });

        Schema::table('web_programs', function (Blueprint $table) {
            $table->dropColumn('alt_image');
        });

        Schema::table('web_partners', function (Blueprint $table) {
            $table->dropColumn('alt_image');
        });

        Schema::table('web_footer_logos', function (Blueprint $table) {
            $table->dropColumn('alt_image');
        });
    }
};
