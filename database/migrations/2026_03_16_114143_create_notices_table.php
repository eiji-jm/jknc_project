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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('notice_number')->nullable();
            $table->date('date_of_notice')->nullable();
            $table->string('governing_body')->nullable();
            $table->string('type_of_meeting')->nullable();
            $table->date('date_of_meeting')->nullable();
            $table->time('time_started')->nullable();
            $table->string('location')->nullable();
            $table->string('meeting_no')->nullable();
            $table->string('chairman')->nullable();
            $table->string('secretary')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->date('date_updated')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
