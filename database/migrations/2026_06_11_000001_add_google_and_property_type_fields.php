<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'google_id')) {
                    $table->string('google_id')->nullable()->after('password');
                }
                if (! Schema::hasColumn('users', 'avatar')) {
                    $table->string('avatar')->nullable()->after('photo');
                }
            });
        }

        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                if (! Schema::hasColumn('properties', 'gender_type')) {
                    $table->enum('gender_type', ['putra', 'putri', 'campuran'])->default('campuran')->after('address');
                }
            });
        }

        if (Schema::hasTable('property_registration_requests')) {
            Schema::table('property_registration_requests', function (Blueprint $table) {
                if (! Schema::hasColumn('property_registration_requests', 'gender_type')) {
                    $table->enum('gender_type', ['putra', 'putri', 'campuran'])->default('campuran')->after('property_address');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('property_registration_requests') && Schema::hasColumn('property_registration_requests', 'gender_type')) {
            Schema::table('property_registration_requests', fn (Blueprint $table) => $table->dropColumn('gender_type'));
        }
        if (Schema::hasTable('properties') && Schema::hasColumn('properties', 'gender_type')) {
            Schema::table('properties', fn (Blueprint $table) => $table->dropColumn('gender_type'));
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'google_id')) $table->dropColumn('google_id');
                if (Schema::hasColumn('users', 'avatar')) $table->dropColumn('avatar');
            });
        }
    }
};
