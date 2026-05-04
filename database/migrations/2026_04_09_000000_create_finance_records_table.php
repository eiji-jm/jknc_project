<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_records', function (Blueprint $table) {
            $table->id();
            $table->string('module_key');
            $table->string('record_number')->nullable();
            $table->string('record_title')->nullable();
            $table->date('record_date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('status')->default('Active');
            $table->string('workflow_status')->default('Uploaded');
            $table->string('approval_status')->default('Pending');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('review_note')->nullable();
            $table->json('data')->nullable();
            $table->json('attachments')->nullable();
            $table->string('share_token')->nullable()->unique();
            $table->timestamp('shared_at')->nullable();
            $table->timestamp('supplier_completed_at')->nullable();
            $table->string('user')->nullable();
            $table->timestamps();

            $table->index(['module_key', 'workflow_status']);
            $table->index(['module_key', 'approval_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_records');
    }
};
