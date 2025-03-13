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
        Schema::create('event_ticket_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id');
            $table->string('transaction_id');
            $table->date('transaction_date');
            $table->string('name');
            $table->string('no_hp');
            $table->string('email');
            $table->boolean('is_alumni')->default(false);
            $table->smallInteger('pax');
            $table->string('payment_method')->nullable();
            $table->double('total_payable');
            $table->tinyInteger('send_email_invoice')->default(0);
            $table->tinyInteger('send_ticket')->default(0);
            $table->string('transaction_status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('log_event_ticket_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->string('transaction_status');
            $table->text('payment_information')->nullable();
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
        Schema::dropIfExists('event_ticket_transactions');
        Schema::dropIfExists('log_event_ticket_transactions');
    }
};
