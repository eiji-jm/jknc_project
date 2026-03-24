<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authority_notes', function (Blueprint $table) {
            $table->id();
            $table->morphs('noteable');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('visible_to_role');
            $table->longText('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authority_notes');
    }
};
