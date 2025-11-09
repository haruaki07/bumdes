<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_request_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained()->onDelete('cascade');
            $table->string('action'); // submitted, approved, rejected, mou_uploaded, mou_signed, disbursed, repayment_made, completed
            $table->text('description');
            $table->foreignId('performed_by')->constrained('users')->onDelete('restrict');
            $table->json('metadata')->nullable(); // Additional data like rejection reason, amount, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_request_timelines');
    }
};
