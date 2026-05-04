<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfer_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_certificates', 'certificate_type')) {
                $table->string('certificate_type')->nullable()->after('company_reg_no');
            }
            if (!Schema::hasColumn('stock_transfer_certificates', 'source_certificate_id')) {
                $table->foreignId('source_certificate_id')->nullable()->constrained('stock_transfer_certificates')->nullOnDelete()->after('installment_id');
            }
            if (!Schema::hasColumn('stock_transfer_certificates', 'issued_to')) {
                $table->string('issued_to')->nullable()->after('stockholder_name');
            }
            if (!Schema::hasColumn('stock_transfer_certificates', 'issued_to_type')) {
                $table->string('issued_to_type')->nullable()->after('issued_to');
            }
            if (!Schema::hasColumn('stock_transfer_certificates', 'released_at')) {
                $table->dateTime('released_at')->nullable()->after('date_issued');
            }
            if (!Schema::hasColumn('stock_transfer_certificates', 'issuance_request_id')) {
                $table->unsignedBigInteger('issuance_request_id')->nullable()->after('source_certificate_id');
            }
        });

        Schema::create('stock_transfer_issuance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->dateTime('requested_at');
            $table->string('request_type');
            $table->string('issuance_type');
            $table->string('requester');
            $table->string('received_by')->nullable();
            $table->string('issued_by')->nullable();
            $table->foreignId('certificate_id')->nullable()->constrained('stock_transfer_certificates')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('stock_transfer_journals')->nullOnDelete();
            $table->foreignId('ledger_id')->nullable()->constrained('stock_transfer_ledgers')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('stock_transfer_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_certificates', 'issuance_request_id')) {
                $table->foreign('issuance_request_id')
                    ->references('id')
                    ->on('stock_transfer_issuance_requests')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfer_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_certificates', 'issuance_request_id')) {
                $table->dropForeign(['issuance_request_id']);
                $table->dropColumn('issuance_request_id');
            }
        });

        Schema::dropIfExists('stock_transfer_issuance_requests');

        Schema::table('stock_transfer_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_certificates', 'released_at')) {
                $table->dropColumn('released_at');
            }
            if (Schema::hasColumn('stock_transfer_certificates', 'issued_to_type')) {
                $table->dropColumn('issued_to_type');
            }
            if (Schema::hasColumn('stock_transfer_certificates', 'issued_to')) {
                $table->dropColumn('issued_to');
            }
            if (Schema::hasColumn('stock_transfer_certificates', 'source_certificate_id')) {
                $table->dropConstrainedForeignId('source_certificate_id');
            }
            if (Schema::hasColumn('stock_transfer_certificates', 'certificate_type')) {
                $table->dropColumn('certificate_type');
            }
        });
    }
};
