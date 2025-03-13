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
        Schema::table('participant', function (Blueprint $table) {
            $table->uuid('unique_code')->nullable();
            $table->string('title')->nullable();
            $table->string('front_title')->nullable();
            $table->string('back_title')->nullable();
            $table->string('fathers_name')->nullable();
            $table->tinyInteger('married_status')->default(1);
            $table->string('nationality')->nullable();
            $table->string('instagram')->nullable();
            $table->string('ktp_province')->nullable();
            $table->string('ktp_city')->nullable();
            $table->string('ktp_kecamatan')->nullable();
            $table->string('ktp_kelurahan')->nullable();
            $table->text('ktp_address')->nullable();
            $table->string('home_province')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_kecamatan')->nullable();
            $table->string('home_kelurahan')->nullable();
            $table->text('home_address')->nullable();
            $table->string('education')->nullable();
            $table->tinyInteger('is_doctor')->default(1);
            $table->string('doctor_specialist')->nullable();
            $table->string('doctor_evidence')->nullable();
            $table->string('job')->nullable();
            $table->string('company_name')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('medical_record')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->text('emergency_address')->nullable();
            $table->tinyInteger('have_passport')->default(1);
            $table->string('name_in_passport')->nullable();
            $table->date('passport_published_date')->nullable();
            $table->date('passport_expired_date')->nullable();
            $table->string('passport_held_by')->nullable();
            $table->integer('refer_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::table('participant', function (Blueprint $table) {
            $table->dropColumn('unique_code');
            $table->dropColumn('title');
            $table->dropColumn('front_title');
            $table->dropColumn('back_title');
            $table->dropColumn('fathers_name');
            $table->dropColumn('married_status');
            $table->dropColumn('nationality');
            $table->dropColumn('instagram');
            $table->dropColumn('ktp_province');
            $table->dropColumn('ktp_city');
            $table->dropColumn('ktp_kecamatan');
            $table->dropColumn('ktp_kelurahan');
            $table->dropColumn('ktp_address');
            $table->dropColumn('home_province');
            $table->dropColumn('home_city');
            $table->dropColumn('home_kecamatan');
            $table->dropColumn('home_kelurahan');
            $table->dropColumn('home_address');
            $table->dropColumn('education');
            $table->dropColumn('is_doctor');
            $table->dropColumn('doctor_specialist');
            $table->dropColumn('doctor_evidence');
            $table->dropColumn('job');
            $table->dropColumn('company_name');
            $table->dropColumn('blood_type');
            $table->dropColumn('medical_record');
            $table->dropColumn('emergency_contact');
            $table->dropColumn('emergency_contact_name');
            $table->dropColumn('emergency_relation');
            $table->dropColumn('emergency_address');
            $table->dropColumn('have_passport');
            $table->dropColumn('name_in_passport');
            $table->dropColumn('passport_published_date');
            $table->dropColumn('passport_expired_date');
            $table->dropColumn('passport_held_by');
            $table->dropColumn('refer_by');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropSoftDeletes();
        });
    }
};
