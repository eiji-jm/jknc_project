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
        Schema::table('stock_transfer_journals', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_journals', 'status')) {
                $table->string('status')->nullable()->after('document_path');
            }
            if (!Schema::hasColumn('stock_transfer_journals', 'reversal_of_id')) {
                $table->foreignId('reversal_of_id')->nullable()->constrained('stock_transfer_journals')->nullOnDelete()->after('status');
            }
        });

        Schema::table('stock_transfer_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_ledgers', 'journal_id')) {
                $table->foreignId('journal_id')->nullable()->constrained('stock_transfer_journals')->nullOnDelete()->after('id');
            }
        });

        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_installments', 'journal_id')) {
                $table->foreignId('journal_id')->nullable()->constrained('stock_transfer_journals')->nullOnDelete()->after('id');
            }
        });

        Schema::table('stock_transfer_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_certificates', 'status')) {
                $table->string('status')->nullable()->after('document_path');
            }
            if (!Schema::hasColumn('stock_transfer_certificates', 'installment_id')) {
                $table->foreignId('installment_id')->nullable()->unique()->constrained('stock_transfer_installments')->nullOnDelete()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfer_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_certificates', 'installment_id')) {
                $table->dropConstrainedForeignId('installment_id');
            }
            if (Schema::hasColumn('stock_transfer_certificates', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('stock_transfer_installments', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_installments', 'journal_id')) {
                $table->dropConstrainedForeignId('journal_id');
            }
        });

        Schema::table('stock_transfer_ledgers', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_ledgers', 'journal_id')) {
                $table->dropConstrainedForeignId('journal_id');
            }
        });

        Schema::table('stock_transfer_journals', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_journals', 'reversal_of_id')) {
                $table->dropConstrainedForeignId('reversal_of_id');
            }
            if (Schema::hasColumn('stock_transfer_journals', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
