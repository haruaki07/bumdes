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
        Schema::table('business_registrations', function (Blueprint $table) {
            $table->integer('revision_number')->default(0)->after('parent_id');
            $table->integer('rejected_at')->nullable()->after('status');
            $table->foreignId('rejected_by')->nullable()->after('rejected_at');

            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_registrations', function (Blueprint $table) {
            $table->dropColumn('revision_number');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_by');
        });
    }
};
