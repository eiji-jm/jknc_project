<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_type');
            $table->string('field_name');
            $table->string('field_key')->unique();
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable();
            $table->string('lookup_module')->nullable();
            $table->string('default_value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_custom_fields');
    }
};
