<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                $table->string('service_id', 5)->unique();
                $table->string('service_name');
                $table->text('service_description');
                $table->text('service_activity_output');
                $table->json('service_area');
                $table->string('service_area_other')->nullable();
                $table->string('category')->nullable();
                $table->string('frequency')->nullable();
                $table->string('schedule_rule')->nullable();
                $table->dateTime('deadline')->nullable();
                $table->string('reminder_lead_time')->nullable();
                $table->json('requirements')->nullable();
                $table->string('requirement_category')->nullable();
                $table->json('engagement_structure');
                $table->boolean('is_recurring')->default(false);
                $table->string('unit')->nullable();
                $table->decimal('rate_per_unit', 12, 2)->nullable();
                $table->unsignedInteger('min_units')->nullable();
                $table->decimal('max_cap', 12, 2)->nullable();
                $table->decimal('price_fee', 12, 2)->nullable();
                $table->decimal('cost_of_service', 12, 2)->nullable();
                $table->string('assigned_unit')->nullable();
                $table->string('status', 30)->default('Draft');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->json('custom_field_values')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('services', function (Blueprint $table) {
                $columns = Schema::getColumnListing('services');

                if (! in_array('company_id', $columns, true)) {
                    $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete()->after('id');
                }
                if (! in_array('service_id', $columns, true)) {
                    $table->string('service_id', 5)->nullable()->after('company_id');
                }
                if (! in_array('service_name', $columns, true)) {
                    $table->string('service_name')->nullable()->after('service_id');
                }
                if (! in_array('service_description', $columns, true)) {
                    $table->text('service_description')->nullable()->after('service_name');
                }
                if (! in_array('service_activity_output', $columns, true)) {
                    $table->text('service_activity_output')->nullable()->after('service_description');
                }
                if (! in_array('service_area', $columns, true)) {
                    $table->json('service_area')->nullable()->after('service_activity_output');
                }
                if (! in_array('service_area_other', $columns, true)) {
                    $table->string('service_area_other')->nullable()->after('service_area');
                }
                if (! in_array('category', $columns, true)) {
                    $table->string('category')->nullable()->after('service_area_other');
                }
                if (! in_array('frequency', $columns, true)) {
                    $table->string('frequency')->nullable()->after('category');
                }
                if (! in_array('schedule_rule', $columns, true)) {
                    $table->string('schedule_rule')->nullable()->after('frequency');
                }
                if (! in_array('deadline', $columns, true)) {
                    $table->dateTime('deadline')->nullable()->after('schedule_rule');
                }
                if (! in_array('reminder_lead_time', $columns, true)) {
                    $table->string('reminder_lead_time')->nullable()->after('deadline');
                }
                if (! in_array('requirements', $columns, true)) {
                    $table->json('requirements')->nullable()->after('reminder_lead_time');
                }
                if (! in_array('requirement_category', $columns, true)) {
                    $table->string('requirement_category')->nullable()->after('requirements');
                }
                if (! in_array('engagement_structure', $columns, true)) {
                    $table->json('engagement_structure')->nullable()->after('requirement_category');
                }
                if (! in_array('is_recurring', $columns, true)) {
                    $table->boolean('is_recurring')->default(false)->after('engagement_structure');
                }
                if (! in_array('unit', $columns, true)) {
                    $table->string('unit')->nullable()->after('is_recurring');
                }
                if (! in_array('rate_per_unit', $columns, true)) {
                    $table->decimal('rate_per_unit', 12, 2)->nullable()->after('unit');
                }
                if (! in_array('min_units', $columns, true)) {
                    $table->unsignedInteger('min_units')->nullable()->after('rate_per_unit');
                }
                if (! in_array('max_cap', $columns, true)) {
                    $table->decimal('max_cap', 12, 2)->nullable()->after('min_units');
                }
                if (! in_array('price_fee', $columns, true)) {
                    $table->decimal('price_fee', 12, 2)->nullable()->after('max_cap');
                }
                if (! in_array('cost_of_service', $columns, true)) {
                    $table->decimal('cost_of_service', 12, 2)->nullable()->after('price_fee');
                }
                if (! in_array('assigned_unit', $columns, true)) {
                    $table->string('assigned_unit')->nullable()->after('cost_of_service');
                }
                if (! in_array('status', $columns, true)) {
                    $table->string('status', 30)->default('Draft')->after('assigned_unit');
                }
                if (! in_array('created_by', $columns, true)) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('status');
                }
                if (! in_array('reviewed_at', $columns, true)) {
                    $table->dateTime('reviewed_at')->nullable()->after('created_by');
                }
                if (! in_array('reviewed_by', $columns, true)) {
                    $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
                }
                if (! in_array('approved_at', $columns, true)) {
                    $table->dateTime('approved_at')->nullable()->after('reviewed_by');
                }
                if (! in_array('approved_by', $columns, true)) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
                }
                if (! in_array('custom_field_values', $columns, true)) {
                    $table->json('custom_field_values')->nullable()->after('approved_by');
                }
            });

        }

        if (! Schema::hasTable('service_custom_fields')) {
            Schema::create('service_custom_fields', function (Blueprint $table) {
                $table->id();
                $table->string('field_name');
                $table->string('field_key')->unique();
                $table->string('field_type', 40);
                $table->boolean('is_required')->default(false);
                $table->json('options')->nullable();
                $table->string('default_value')->nullable();
                $table->string('lookup_module')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_custom_fields');
        Schema::dropIfExists('services');
    }
};
