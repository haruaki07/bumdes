<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('disbursed_by')->constrained('users')->onDelete('restrict');
            $table->date('disbursement_date');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['transfer', 'tunai'])->default('transfer');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('proof_document')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_disbursements');
    }
};
