<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('policy')->nullable();
            $table->string('version')->nullable();
            $table->date('effectivity_date')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('classification')->nullable();
            $table->longText('description')->nullable();

            $table->string('approval_status')->default('Pending');
            $table->string('workflow_status')->default('Submitted');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('review_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
