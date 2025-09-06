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
        Schema::create('ebil_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('brand_logo')->nullable();
            $table->string('description')->nullable();
            $table->enum('type', ['BANK_TRANSFER', 'RETAIL', 'QR', 'VIRTUAL_ACCOUNT']);
            $table->string('account_number')->nullable(); // For bank transfer methods
            $table->boolean('is_active')->default(true);
            $table->boolean('need_confirmation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebil_payment_methods');
    }
};
