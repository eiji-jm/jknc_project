<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_book_issuance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->unique();
            $table->date('date_requested')->nullable();
            $table->time('time_requested')->nullable();
            $table->string('type_of_request')->nullable();
            $table->string('requester')->nullable();
            $table->string('received_by')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_book_issuance_requests');
    }
};