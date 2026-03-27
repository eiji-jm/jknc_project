<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
Schema::create('bylaws', function (Blueprint $table) {

$table->id();

$table->string('corporation_name');
$table->string('company_reg_no');
$table->string('type_of_formation')->nullable();

$table->string('aoi_version')->nullable();
$table->string('aoi_type')->nullable();
$table->date('aoi_date')->nullable();

$table->string('regular_asm')->nullable();
$table->string('asm_notice')->nullable();

$table->string('regular_bodm')->nullable();
$table->string('bodm_notice')->nullable();

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
        Schema::dropIfExists('bylaws');
    }
};
