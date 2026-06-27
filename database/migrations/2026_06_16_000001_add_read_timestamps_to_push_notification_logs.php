<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('push_notification_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('push_notification_logs', 'tenant_read_at')) {
                $table->timestamp('tenant_read_at')->nullable()->after('sent_at');
            }

            if (! Schema::hasColumn('push_notification_logs', 'admin_read_at')) {
                $table->timestamp('admin_read_at')->nullable()->after('tenant_read_at');
            }

            if (! Schema::hasColumn('push_notification_logs', 'super_admin_read_at')) {
                $table->timestamp('super_admin_read_at')->nullable()->after('admin_read_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('push_notification_logs', function (Blueprint $table) {
            if (Schema::hasColumn('push_notification_logs', 'super_admin_read_at')) {
                $table->dropColumn('super_admin_read_at');
            }
            if (Schema::hasColumn('push_notification_logs', 'admin_read_at')) {
                $table->dropColumn('admin_read_at');
            }
            if (Schema::hasColumn('push_notification_logs', 'tenant_read_at')) {
                $table->dropColumn('tenant_read_at');
            }
        });
    }
};
