<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizational_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('country')->default('Philippines');
            $table->string('region_code', 20);
            $table->string('region_name');
            $table->string('province_code', 20)->nullable();
            $table->string('province_name')->nullable();
            $table->string('province_type', 50)->nullable(); // province or district
            $table->string('city_code', 20);
            $table->string('city_name');
            $table->string('barangay_code', 20);
            $table->string('barangay_name');
            $table->text('street_address');
            $table->string('subdivision_building')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('full_address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_addresses');
    }
};