<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebil_payment_methods', function (Blueprint $table) {
            $table->enum('fee_type', ['NONE', 'PERCENT', 'FIXED'])->default('NONE')->after('need_confirmation');
            $table->decimal('fee_amount', 12, 2)->default(0)->after('fee_type');
        });
    }

    public function down(): void
    {
        Schema::table('ebil_payment_methods', function (Blueprint $table) {
            $table->dropColumn(['fee_type', 'fee_amount']);
        });
    }
};
