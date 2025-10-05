<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id'); // Primary key AUTO_INCREMENT
            $table->string('customer_name', 100);
            $table->date('dob')->nullable();
            $table->string('door_street', 100);
            $table->string('area', 100);
            $table->string('taluk', 100);
            $table->string('district', 100);
            $table->string('state', 50);
            $table->string('pincode', 10);
            $table->string('mobile_no', 15);
            $table->string('email_id', 100)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('aadhar_no', 20);
            $table->string('gst_no', 20)->nullable();
            $table->string('bank_name', 100);
            $table->string('account_no', 50);
            $table->string('ifsc_code', 20);
            $table->string('micr_code', 20)->nullable();
            $table->string('branch', 100);
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
