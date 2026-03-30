<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ultimate_beneficial_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gis_id')->constrained('gis_records')->cascadeOnDelete();

            $table->string('complete_name');
            $table->string('specific_residential_address')->nullable();
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('tax_identification_no')->nullable();
            $table->decimal('ownership_voting_rights', 5, 2)->nullable();
            $table->enum('beneficial_owner_type', ['D', 'I'])->nullable();
            $table->enum('beneficial_ownership_category', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ultimate_beneficial_owners');
    }
};