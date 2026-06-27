<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->string('bank_name', 100);
            $table->string('account_number', 100);
            $table->string('account_name', 150);
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('property_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
