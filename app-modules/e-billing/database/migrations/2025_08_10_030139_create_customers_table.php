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
        Schema::create('ebil_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('map_url')->nullable();
            $table->integer('due')->unsigned();
            $table->string('serial_number')->nullable();
            $table->string('mac_address')->nullable();
            $table->dateTime('registration_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'isolate'])->default('active');
            $table->foreignId('site_id')->constrained('ebil_sites');
            $table->foreignId('package_id')->constrained('ebil_packages');
            $table->foreignId('device_id')->nullable()->constrained('ebil_devices');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebil_customers');
    }
};
