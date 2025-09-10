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
        Schema::table('ebil_invoices', function (Blueprint $table) {
            $table->date('due_date')->after('invoice_number');
            $table->date('grace_period_end_date')->after('due_date');
            $table->date('period_start_date')->after('grace_period_end_date');
            $table->date('period_end_date')->after('period_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebil_invoices', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'grace_period_end_date', 'period_start_date', 'period_end_date']);
        });
    }
};
