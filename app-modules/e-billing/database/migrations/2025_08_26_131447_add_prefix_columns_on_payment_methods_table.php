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
        Schema::table('ebil_payment_methods', function (Blueprint $table) {
            $table->integer('prefix_length')->after('type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebil_payment_methods', function (Blueprint $table) {
            $table->dropColumn('prefix_length');
        });
    }
};
