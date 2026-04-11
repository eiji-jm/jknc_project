<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_book_certificate_cancellations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_book_certificate_id');
            $table->date('date_of_cancellation')->nullable();
            $table->date('effective_date')->nullable();
            $table->text('reason')->nullable();
            $table->string('type_of_cancellation')->nullable();
            $table->string('others_specify')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('stock_transfer_book_certificate_id', 'stb_cert_cancel_cert_fk')
                ->references('id')
                ->on('stock_transfer_book_certificates')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_book_certificate_cancellations');
    }
};