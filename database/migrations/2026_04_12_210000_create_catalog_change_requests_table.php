<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('catalog_change_requests')) {
            return;
        }

        Schema::create('catalog_change_requests', function (Blueprint $table) {
            $table->id();
            $table->string('module', 30);
            $table->unsignedBigInteger('record_id');
            $table->string('record_public_id')->nullable();
            $table->string('record_name')->nullable();
            $table->string('action', 20);
            $table->json('payload')->nullable();
            $table->string('status', 30)->default('Pending Approval');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_notes')->nullable();
            $table->timestamps();

            $table->index(['module', 'status']);
            $table->index(['module', 'record_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_change_requests');
    }
};
