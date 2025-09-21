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
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->string('menu')->nullable()->after('description');
            $table->string('menu_group')->nullable()->after('menu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('menu');
            $table->dropColumn('menu_group');
        });
    }
};
