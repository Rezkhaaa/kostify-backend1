<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('owner_name')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->enum('gender_type', ['putra', 'putri', 'campuran'])->default('campuran');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('package_name')->default('Basic');
                $table->unsignedInteger('max_units')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','property_admin','admin','tenant') NOT NULL DEFAULT 'tenant'");
        }

        foreach (['users','units','bookings','occupancies','billings','payments','complaints','maintenances','activity_histories','tenant_registration_requests','password_reset_requests'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'property_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('property_id')->nullable()->index()->after('id');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['password_reset_requests','tenant_registration_requests','activity_histories','maintenances','complaints','payments','billings','occupancies','bookings','units','users'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'property_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('property_id');
                });
            }
        }
        Schema::dropIfExists('properties');
    }
};
