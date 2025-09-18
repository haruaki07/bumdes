<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebil_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('ebil_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('ebil_users')->nullOnDelete();
            $table->string('author_name')->nullable(); // for customer public portal if not authenticated
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebil_ticket_messages');
    }
};
