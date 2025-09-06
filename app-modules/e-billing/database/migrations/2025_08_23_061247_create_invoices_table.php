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
        Schema::create('ebil_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('ebil_customers')->nullOnDelete();
            $table->json('customer_detail');
            $table->foreignId('package_id')->nullable()->constrained('ebil_packages')->nullOnDelete();
            $table->json('package_detail');
            $table->decimal('total_price', 10, 2);
            $table->string('payment_session_url');
            $table->enum('status', ['unpaid', 'paid', 'processing', 'completed', 'canceled', 'expired'])->default('unpaid');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebil_invoices');
    }
};
