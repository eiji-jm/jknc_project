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
        Schema::create('secretary_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_no')->nullable();
            $table->date('date_uploaded')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->string('governing_body')->nullable();
            $table->string('type_of_meeting')->nullable();
            $table->string('notice_ref')->nullable();
            $table->string('meeting_no')->nullable();
            $table->string('resolution_no')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('purpose')->nullable();
            $table->date('date_of_meeting')->nullable();
            $table->string('location')->nullable();
            $table->string('secretary')->nullable();
            $table->string('notary_doc_no')->nullable();
            $table->string('notary_page_no')->nullable();
            $table->string('notary_book_no')->nullable();
            $table->string('notary_series_no')->nullable();
            $table->string('notary_public')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secretary_certificates');
    }
};
