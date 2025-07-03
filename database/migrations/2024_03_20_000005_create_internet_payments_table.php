<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internet_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internet_service_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('payment_method', ['cash', 'transfer', 'other']);
            $table->string('payment_proof')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internet_payments');
    }
};
