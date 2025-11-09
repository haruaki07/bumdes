<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            // Approval tracking
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Rejection tracking
            $table->foreignId('rejected_by')->nullable()->after('rejection_reason')->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');

            // MOU tracking
            $table->foreignId('mou_uploaded_by')->nullable()->after('mou_document')->constrained('users')->onDelete('set null');
            $table->timestamp('mou_uploaded_at')->nullable()->after('mou_uploaded_by');
            $table->timestamp('mou_signed_at')->nullable()->after('signature_document');

            // Funding terms
            $table->decimal('interest_rate', 5, 2)->default(0)->after('amount')->comment('Percentage');
            $table->integer('repayment_duration_months')->nullable()->after('interest_rate');
        });

        // Update status enum to include new statuses
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status ENUM('submitted', 'approved', 'mou_signed', 'ready_to_disburse', 'disbursed', 'repaying', 'completed', 'rejected') DEFAULT 'submitted'");
    }

    public function down(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');

            $table->dropForeign(['rejected_by']);
            $table->dropColumn('rejected_by');
            $table->dropColumn('rejected_at');

            $table->dropForeign(['mou_uploaded_by']);
            $table->dropColumn('mou_uploaded_by');
            $table->dropColumn('mou_uploaded_at');
            $table->dropColumn('mou_signed_at');

            $table->dropColumn('interest_rate');
            $table->dropColumn('repayment_duration_months');
        });

        // Revert status enum back to original
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status ENUM('submitted', 'approved', 'rejected', 'disbursed', 'completed') DEFAULT 'submitted'");
    }
};
