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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('department_id');
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('respons')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('form_id');
            $table->text('question');
            $table->string('type')->comment('text,textarea,checkbox,radio,option,file,date,email,number,date,time');
            $table->boolean('required')->default(false);;
            $table->text('option_value')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_answers', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id');
            $table->string('name')->nullable();
            $table->string('no_hp')->nullable();
            $table->integer('question_id');
            $table->text('answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
        Schema::dropIfExists('form_questions');
        Schema::dropIfExists('form_answers');
    }
};
