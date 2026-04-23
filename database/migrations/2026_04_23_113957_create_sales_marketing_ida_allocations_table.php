<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_marketing_ida_allocations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ida_id');
            $table->unsignedBigInteger('earner_id')->nullable();

            $table->string('role')->nullable();
            $table->string('commission_category')->nullable();
            $table->string('commission_type')->nullable();

            $table->decimal('commission_rate', 10, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);

            $table->string('status')->default('Pending');

            $table->timestamps();

            $table->foreign('ida_id')
                ->references('id')
                ->on('sales_marketing_idas')
                ->onDelete('cascade');

            $table->foreign('earner_id')
                ->references('id')
                ->on('sales_marketing_earners')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_marketing_ida_allocations');
    }
};