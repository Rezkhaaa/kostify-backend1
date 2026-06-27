<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan status akses penghuni per kos.
     * Contoh: Vita bisa dinonaktifkan di Kos Melati, tetapi tetap aktif untuk Kos Mawar.
     */
    public function up(): void
    {
        Schema::create('tenant_property_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('disabled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disabled_at')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'property_id']);
            $table->index(['property_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_property_statuses');
    }
};
