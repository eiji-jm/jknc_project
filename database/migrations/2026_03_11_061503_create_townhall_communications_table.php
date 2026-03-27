<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('townhall_communications', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable()->unique();
            $table->date('communication_date')->nullable();
            $table->string('from_name')->nullable();
            $table->string('department_stakeholder')->nullable();
            $table->string('to_for')->nullable();
            $table->string('status')->default('Open');
            $table->string('subject')->nullable();
            $table->longText('message')->nullable();
            $table->string('cc')->nullable();
            $table->string('additional')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('townhall_communications');
    }
};
