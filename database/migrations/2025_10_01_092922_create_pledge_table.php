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
        Schema::create('pledges', function (Blueprint $table) {
            $table->bigIncrements('pledge_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name', 100);
            $table->string('loan_id', 20);
            $table->string('ornament_name', 100);
            $table->enum('ornament_nature', ['Silver', 'Gold', 'Platinum']);
            $table->date('date_of_pledge');
            $table->decimal('current_rate_per_gram', 10, 2);
            $table->decimal('weight', 10, 2);
            $table->decimal('fixed_percent_loan', 5, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->date('date_of_maturity');
            $table->decimal('late_payment_interest', 5, 2);
            $table->decimal('amount', 12, 2);
            $table->decimal('sgst', 7, 2);
            $table->decimal('cgst', 7, 2);
            $table->decimal('grand_total', 12, 2);
            $table->string('image_upload', 255)->nullable();
            $table->string('aadhar_upload', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pledges');
    }
};
