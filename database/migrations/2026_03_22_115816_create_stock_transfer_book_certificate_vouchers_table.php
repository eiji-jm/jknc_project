<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_book_certificate_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_book_certificate_id');
            $table->date('date_uploaded')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->string('corporation_name')->nullable();
            $table->string('company_reg_no')->nullable();
            $table->string('stock_number')->nullable();
            $table->string('stockholder_name')->nullable();
            $table->string('par_value')->nullable();
            $table->integer('number')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('amount_in_words')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('president')->nullable();
            $table->string('corporate_secretary')->nullable();
            $table->string('issued_to')->nullable();
            $table->string('issued_to_type')->nullable();
            $table->date('certificate_released_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('stock_transfer_book_certificate_id', 'stb_cert_voucher_cert_fk')
                ->references('id')
                ->on('stock_transfer_book_certificates')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_book_certificate_vouchers');
    }
};