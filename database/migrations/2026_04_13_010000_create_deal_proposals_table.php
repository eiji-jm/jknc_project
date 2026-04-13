<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->string('reference_id')->nullable();
            $table->string('crud_id')->nullable();
            $table->date('proposal_date')->nullable();
            $table->string('location')->nullable();
            $table->string('service_type')->nullable();
            $table->longText('scope_of_service')->nullable();
            $table->longText('what_you_will_receive')->nullable();
            $table->longText('our_proposal_text')->nullable();
            $table->longText('requirements_sole')->nullable();
            $table->longText('requirements_juridical')->nullable();
            $table->longText('requirements_optional')->nullable();
            $table->decimal('price_regular', 15, 2)->nullable();
            $table->decimal('price_discount', 15, 2)->nullable();
            $table->decimal('price_subtotal', 15, 2)->nullable();
            $table->decimal('price_tax', 15, 2)->nullable();
            $table->decimal('price_total', 15, 2)->nullable();
            $table->decimal('price_down', 15, 2)->nullable();
            $table->decimal('price_balance', 15, 2)->nullable();
            $table->string('prepared_by_name')->nullable();
            $table->string('prepared_by_id')->nullable();
            $table->timestamps();

            $table->unique('deal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_proposals');
    }
};
