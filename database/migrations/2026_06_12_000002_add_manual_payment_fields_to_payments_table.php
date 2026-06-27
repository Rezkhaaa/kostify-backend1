<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'sender_name')) {
                $table->string('sender_name')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('payments', 'sender_bank')) {
                $table->string('sender_bank')->nullable()->after('sender_name');
            }
            if (! Schema::hasColumn('payments', 'transfer_date')) {
                $table->date('transfer_date')->nullable()->after('sender_bank');
            }
            if (! Schema::hasColumn('payments', 'proof_image')) {
                $table->string('proof_image')->nullable()->after('transfer_date');
            }
            if (! Schema::hasColumn('payments', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('proof_image');
            }
            if (! Schema::hasColumn('payments', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (! Schema::hasColumn('payments', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_account_number');
            }
            if (! Schema::hasColumn('payments', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('bank_account_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach ([
                'admin_note', 'bank_account_name', 'bank_account_number', 'bank_name',
                'proof_image', 'transfer_date', 'sender_bank', 'sender_name'
            ] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
