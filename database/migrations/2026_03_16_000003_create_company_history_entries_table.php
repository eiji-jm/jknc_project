<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_history_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('type');
            $table->string('title');
            $table->text('description');
            $table->string('extra_label')->nullable();
            $table->string('extra_value')->nullable();
            $table->string('user_name');
            $table->string('user_initials', 10);
            $table->dateTime('occurred_at');
            $table->timestamps();

            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_history_entries');
    }
};
