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
        Schema::table('warga_profiles', function (Blueprint $table) {
            // Make NIK, phone, and address nullable (will be filled after email verification)
            $table->string('nik', 16)->nullable()->change();
            $table->string('phone', 20)->nullable()->change();
            $table->text('address')->nullable()->change();

            // Drop unique constraint on NIK temporarily
            $table->dropUnique(['nik']);
        });

        // Add unique constraint back but allow NULL values
        Schema::table('warga_profiles', function (Blueprint $table) {
            $table->unique('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warga_profiles', function (Blueprint $table) {
            // Revert back to non-nullable
            $table->string('nik', 16)->nullable(false)->change();
            $table->string('phone', 20)->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
        });
    }
};
