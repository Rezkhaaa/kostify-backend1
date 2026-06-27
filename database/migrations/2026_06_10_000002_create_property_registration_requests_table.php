<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('property_name');
            $table->text('property_address')->nullable();
            $table->enum('gender_type', ['putra', 'putri', 'campuran'])->default('campuran');
            $table->unsignedInteger('room_count')->nullable();
            $table->string('password');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('created_property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('created_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_registration_requests');
    }
};
