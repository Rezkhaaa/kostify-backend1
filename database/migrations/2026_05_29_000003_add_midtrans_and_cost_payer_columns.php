<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'order_id')) {
                $table->string('order_id')->nullable()->index()->after('payment_code');
            }
            if (! Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->text('snap_redirect_url')->nullable()->after('snap_token');
            }
            if (! Schema::hasColumn('payments', 'gateway_response')) {
                $table->longText('gateway_response')->nullable()->after('snap_redirect_url');
            }
        });

        Schema::table('maintenances', function (Blueprint $table) {
            if (! Schema::hasColumn('maintenances', 'cost_payer')) {
                $table->enum('cost_payer', ['pengelola', 'tenant', 'belum_ditentukan'])
                    ->default('belum_ditentukan')
                    ->after('cost');
            }
        });
    }

    public function down(): void
    {
        // Kolom dibiarkan agar aman untuk data produksi.
    }
};
