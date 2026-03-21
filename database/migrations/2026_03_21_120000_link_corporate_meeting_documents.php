<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->longText('body_html')->nullable()->after('date_updated');
            $table->string('body_mode')->nullable()->after('body_html');
        });

        Schema::table('minutes', function (Blueprint $table) {
            $table->foreignId('notice_id')->nullable()->after('meeting_mode')->constrained('notices')->nullOnDelete();
        });

        Schema::table('resolutions', function (Blueprint $table) {
            $table->foreignId('notice_id')->nullable()->after('type_of_meeting')->constrained('notices')->nullOnDelete();
            $table->longText('resolution_body')->nullable()->after('board_resolution');
            $table->date('notarized_on')->nullable()->after('notary_public');
            $table->string('notarized_at')->nullable()->after('notarized_on');
        });

        Schema::table('secretary_certificates', function (Blueprint $table) {
            $table->foreignId('notice_id')->nullable()->after('type_of_meeting')->constrained('notices')->nullOnDelete();
            $table->foreignId('resolution_id')->nullable()->after('notice_id')->constrained('resolutions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('secretary_certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('resolution_id');
            $table->dropConstrainedForeignId('notice_id');
        });

        Schema::table('resolutions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('notice_id');
            $table->dropColumn(['resolution_body', 'notarized_on', 'notarized_at']);
        });

        Schema::table('minutes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('notice_id');
        });

        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['body_html', 'body_mode']);
        });
    }
};
