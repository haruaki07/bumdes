<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internet_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('installation_address');
            $table->string('package_name');
            $table->decimal('monthly_fee', 10, 2);
            $table->enum('status', ['active', 'suspended', 'terminated'])->default('active');
            $table->text('suspension_reason')->nullable();
            $table->date('suspension_date')->nullable();
            $table->string('contact_phone');
            $table->string('contact_whatsapp')->nullable();
            $table->date('installation_date');
            $table->date('next_billing_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internet_services');
    }
};
