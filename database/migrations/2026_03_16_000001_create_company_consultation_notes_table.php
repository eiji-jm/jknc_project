<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_consultation_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->date('consultation_date');
            $table->string('author')->nullable();
            $table->string('category')->nullable();
            $table->string('linked_deal')->nullable();
            $table->string('linked_activity')->nullable();
            $table->text('summary')->nullable();
            $table->longText('details')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_consultation_notes');
    }
};
