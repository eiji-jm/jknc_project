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
    Schema::create('sec_aois', function (Blueprint $table) {
        $table->id();
        $table->string('corporation_name');
        $table->string('company_reg_no');
        $table->string('principal_address');
        $table->string('par_value')->nullable();
        $table->string('authorized_capital_stock')->nullable();
        $table->integer('directors')->nullable();
        $table->string('type_of_formation')->nullable();
        $table->string('aoi_version')->nullable();
        $table->string('aoi_type')->nullable();
        $table->string('uploaded_by')->nullable();
        $table->date('date_upload')->nullable();
        $table->string('file_path')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sec_aois');
    }
};
