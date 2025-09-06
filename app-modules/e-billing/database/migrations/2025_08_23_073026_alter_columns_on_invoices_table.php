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
            $table->renameColumn('total_price', 'amount');
            $table->string('payment_session_url')->nullable()->after('amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebil_invoices', function (Blueprint $table) {
            $table->renameColumn('amount', 'total_price');
            $table->string('payment_session_url')->nullable(false)->after('total_price')->change();
        });
    }
};
