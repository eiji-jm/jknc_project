<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id', 5)->unique();
            $table->string('product_name');
            $table->string('product_type');
            $table->unsignedBigInteger('linked_service_id')->nullable();
            $table->json('product_area');
            $table->string('product_area_other')->nullable();
            $table->text('product_description');
            $table->text('product_inclusions')->nullable();
            $table->string('category');
            $table->string('pricing_type');
            $table->decimal('price', 15, 2);
            $table->decimal('cost', 15, 2)->nullable();
            $table->boolean('is_discountable')->default(false);
            $table->string('tax_type');
            $table->string('sku')->nullable()->unique();
            $table->string('inventory_type');
            $table->integer('stock_qty')->nullable();
            $table->string('unit')->nullable();
            $table->string('status');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('custom_field_values')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
