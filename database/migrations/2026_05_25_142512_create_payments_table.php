<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique();
            $table->string('order_id')->nullable()->index();
            $table->foreignId('billing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['midtrans','transfer','cash','qris','virtual_account','admin_confirmation'])->default('transfer');
            $table->enum('status', ['pending','success','failed'])->default('pending');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->longText('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
