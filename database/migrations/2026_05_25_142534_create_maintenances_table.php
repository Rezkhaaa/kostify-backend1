<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('maintenance_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['plumbing', 'electrical', 'furniture', 'ac', 'painting', 'structural', 'lainnya'])->default('lainnya');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'approved', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->enum('cost_payer', ['pengelola', 'tenant', 'belum_ditentukan'])->default('belum_ditentukan');
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};