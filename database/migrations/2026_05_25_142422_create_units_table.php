<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code')->unique();
            $table->string('name');
            $table->enum('type', ['standar', 'premium', 'deluxe', 'ac', 'non_ac', 'kamar_mandi_dalam', 'eksklusif'])->default('standar');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->enum('price_period', ['bulanan', 'tahunan'])->default('bulanan');
            $table->integer('floor')->nullable()->comment('jumlah lantai');
            $table->decimal('area', 8, 2)->nullable()->comment('luas m2');
            $table->integer('capacity')->default(1)->comment('kapasitas penghuni');
            $table->json('facilities')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'inactive'])->default('available');
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
