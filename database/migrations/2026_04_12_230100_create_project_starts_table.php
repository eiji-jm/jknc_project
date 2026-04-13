<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_starts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('start_code')->unique();
            $table->date('date_started')->nullable();
            $table->date('date_completed')->nullable();
            $table->string('status')->default('pending');
            $table->json('checklist')->nullable();
            $table->json('engagement_requirements')->nullable();
            $table->json('routing')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_by_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_starts');
    }
};
