<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gis_records', function (Blueprint $table) {

            $table->id();

            $table->string('uploaded_by')->nullable();
            $table->string('submission_status')->nullable();
            $table->date('receive_on')->nullable();
            $table->string('period_date')->nullable();

            $table->string('company_reg_no')->nullable();
            $table->string('corporation_name')->nullable();

            $table->date('annual_meeting')->nullable();
            $table->string('meeting_type')->nullable();

            $table->string('file')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gis_records');
    }
};