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
        Schema::table('ebil_customers', function (Blueprint $table) {
            // for now, we only support monthly billing
            $table->enum('bill_cycle', ['monthly'])->default('monthly')->after('package_id');
            $table->integer('due_reminder_days')->default(5)->after('bill_cycle');
            $table->date('next_billing_date')->nullable()->after('due_reminder_days');
            $table->string('invoice_number')->nullable()->after('next_billing_date');
            $table->integer('grace_period')->default(0)->after('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebil_customers', function (Blueprint $table) {
            $table->dropColumn(['bill_cycle', 'due_reminder_days', 'next_billing_date', 'invoice_number', 'grace_period']);
        });
    }
};
