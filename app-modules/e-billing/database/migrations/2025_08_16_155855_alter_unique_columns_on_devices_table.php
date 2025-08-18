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
        Schema::table('ebil_devices', function (Blueprint $table) {
            $table->dropUnique(['brand']);
            $table->unique(['brand', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebil_devices', function (Blueprint $table) {
            $table->dropUnique(['brand', 'model']);
            $table->string('brand')->unique()->change();
        });
    }
};
