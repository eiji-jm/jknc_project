<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permits', function (Blueprint $table) {
            $table->id();
            $table->string('permit_type'); // Mayor's Permit, Fire Permit, etc.
            $table->date('date');
            $table->string('user'); // uploader
            $table->string('client');
            $table->string('tin');
            $table->string('registration_status');
            $table->string('status'); // Active, Overdue, For Review
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permits');
    }
};
