<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('townhall_acknowledgements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('townhall_communication_id')->constrained('townhall_communications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->unique(['townhall_communication_id', 'user_id'], 'tha_comm_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('townhall_acknowledgements');
    }
};
