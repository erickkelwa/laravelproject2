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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('checkout_request_id')->unique();
            $table->string('merchant_request_id');
            $table->decimal('amount', 10, 2);
            $table->string('phone_number');
            $table->string('status')->default('pending'); // pending, success, failed
            $table->string('receipt_number')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->text('result_desc')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
