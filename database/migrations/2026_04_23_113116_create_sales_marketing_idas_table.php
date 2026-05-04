<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_marketing_idas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('deal_id')->nullable();
            $table->string('condeal_ref_no')->nullable();

            $table->string('client_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('service_area')->nullable();
            $table->string('product_engagement_structure')->nullable();

            $table->decimal('deal_value', 15, 2)->default(0);

            $table->string('workflow_status')->default('Uploaded');
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_marketing_idas');
    }
};