<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_sows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('sow_number')->unique();
            $table->string('version_number')->nullable();
            $table->date('date_prepared')->nullable();
            $table->json('within_scope_items')->nullable();
            $table->json('out_of_scope_items')->nullable();
            $table->string('client_confirmation_name')->nullable();
            $table->json('internal_approval')->nullable();
            $table->string('approval_status')->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->string('ntp_status')->default('pending');
            $table->string('client_signed_attachment_path')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_sows');
    }
};
