<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenant_registration_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('tenant_registration_requests', 'google_id')) {
                $table->string('google_id')->nullable()->after('password');
            }
            if (! Schema::hasColumn('tenant_registration_requests', 'avatar')) {
                $table->text('avatar')->nullable()->after('google_id');
            }
            if (! Schema::hasColumn('tenant_registration_requests', 'gender')) {
                $table->string('gender', 20)->nullable()->after('avatar');
            }
            if (! Schema::hasColumn('tenant_registration_requests', 'requested_via')) {
                $table->string('requested_via', 20)->default('manual')->after('gender');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenant_registration_requests', function (Blueprint $table) {
            foreach (['requested_via', 'gender', 'avatar', 'google_id'] as $column) {
                if (Schema::hasColumn('tenant_registration_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
