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
        Schema::create('warga_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('kk', 16)->nullable(); // Kartu Keluarga
            $table->string('phone', 20); // WhatsApp number
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable(); // Laki-laki, Perempuan
            $table->text('address');
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('occupation')->nullable(); // Pekerjaan
            $table->enum('marital_status', ['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])->nullable();
            $table->string('religion')->nullable(); // Agama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga_profiles');
    }
};
