<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specimen_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->string('bif_no')->nullable();
            $table->date('date')->nullable();
            $table->string('client_type')->nullable();
            $table->string('business_name_left')->nullable();
            $table->string('business_name_right')->nullable();
            $table->string('account_number_left')->nullable();
            $table->string('account_number_right')->nullable();
            $table->json('signatories')->nullable();
            $table->json('authentication_data')->nullable();
            $table->text('remarks')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specimen_signatures');
    }
};
