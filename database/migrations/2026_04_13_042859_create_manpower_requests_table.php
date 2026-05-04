<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manpower_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->string('department')->nullable();
            $table->date('date_requested')->nullable();
            $table->date('date_required')->nullable();
            $table->string('position')->nullable();
            $table->string('employment_type')->nullable();
            $table->text('duties')->nullable();
            $table->string('nature_of_request')->nullable();
            $table->string('age_range')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('gender')->nullable();
            $table->integer('headcount')->nullable();
            $table->string('education')->nullable();
            $table->text('qualifications')->nullable();
            $table->string('requested_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->text('remarks')->nullable();
            $table->string('request_status')->default('Pending');
            $table->string('charged_to')->nullable();
            $table->text('breakdown_details')->nullable();
            $table->string('hired_personnel')->nullable();
            $table->date('date_hired')->nullable();
            $table->string('processed_by')->nullable();
            $table->string('checked_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manpower_requests');
    }
};
