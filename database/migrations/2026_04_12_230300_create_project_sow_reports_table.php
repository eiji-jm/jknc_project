<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_sow_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('report_number')->unique();
            $table->string('version_number')->nullable();
            $table->date('date_prepared')->nullable();
            $table->json('within_scope_items')->nullable();
            $table->json('out_of_scope_items')->nullable();
            $table->json('status_summary')->nullable();
            $table->decimal('project_completion_percentage', 5, 2)->nullable();
            $table->text('key_issues')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('way_forward')->nullable();
            $table->string('client_confirmation_name')->nullable();
            $table->json('internal_approval')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_sow_reports');
    }
};
