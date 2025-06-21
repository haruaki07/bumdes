<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('funding_requests', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('restrict');
      $table->foreignId('business_id')->nullable()->constrained()->onDelete('restrict');
      $table->decimal('amount', 15, 2);
      $table->text('purpose');
      $table->enum('status', ['submitted', 'approved', 'rejected', 'disbursed', 'completed'])->default('submitted');
      $table->text('rejection_reason')->nullable();
      $table->string('mou_document')->nullable();
      $table->boolean('is_mou_approved')->default(false);
      $table->string('signature_document')->nullable();
      $table->date('disbursement_date')->nullable();
      $table->date('due_date')->nullable();
      $table->decimal('disbursed_amount', 15, 2)->nullable();
      $table->decimal('repaid_amount', 15, 2)->default(0);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('funding_requests');
  }
};
