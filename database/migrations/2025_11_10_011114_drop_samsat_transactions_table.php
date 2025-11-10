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
        Schema::dropIfExists('samsat_transactions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('samsat_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('vehicle_number');
            $table->string('vehicle_type');
            $table->integer('vehicle_year');
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('admin_fee', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->date('payment_date');
            $table->date('tax_period_start');
            $table->date('tax_period_end');
            $table->string('receipt_number')->unique();
            $table->enum('payment_method', ['cash', 'transfer']);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
