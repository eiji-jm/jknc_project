<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_ntps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('ntp_number')->unique();
            $table->string('reference_type')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('date_issued')->nullable();
            $table->json('payload')->nullable();
            $table->string('client_access_token')->nullable();
            $table->dateTime('client_access_expires_at')->nullable();
            $table->string('client_form_sent_to_email')->nullable();
            $table->dateTime('client_form_sent_at')->nullable();
            $table->string('client_response_status')->nullable();
            $table->dateTime('client_approved_at')->nullable();
            $table->string('client_approved_name')->nullable();
            $table->text('client_response_notes')->nullable();
            $table->string('client_attachment_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_ntps');
    }
};
