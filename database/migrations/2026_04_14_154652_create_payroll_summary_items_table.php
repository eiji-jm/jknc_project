<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_summary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_summary_id')->constrained('payroll_summaries')->cascadeOnDelete();
            $table->enum('item_type', ['earning', 'deduction', 'info']);
            $table->string('category');
            $table->string('name');
            $table->decimal('amount', 12, 2)->default(0);
            $table->json('meta_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_summary_items');
    }
};