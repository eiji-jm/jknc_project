<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sec_coi', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_name');
            $table->string('company_reg_no');
            $table->string('issued_by');
            $table->date('issued_on');
            $table->date('date_upload');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sec_coi');
    }
};