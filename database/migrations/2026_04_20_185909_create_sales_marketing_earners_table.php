<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_marketing_earners', function (Blueprint $table) {
            $table->id();
            $table->string('source_type')->default('manual'); // manual, contact, employee
            $table->unsignedBigInteger('source_id')->nullable();

            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('mobile_number')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('tin')->nullable();

            $table->string('status')->default('Active');
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_marketing_earners');
    }
};