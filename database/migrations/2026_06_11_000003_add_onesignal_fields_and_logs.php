<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('onesignal_external_id')->nullable()->after('avatar')->index();
            $table->string('onesignal_subscription_id')->nullable()->after('onesignal_external_id')->index();
            $table->boolean('onesignal_enabled')->default(true)->after('onesignal_subscription_id');
            $table->timestamp('onesignal_last_synced_at')->nullable()->after('onesignal_enabled');
        });

        Schema::create('push_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->string('onesignal_notification_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'skipped'])->default('pending');
            $table->json('response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['property_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notification_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'onesignal_external_id',
                'onesignal_subscription_id',
                'onesignal_enabled',
                'onesignal_last_synced_at',
            ]);
        });
    }
};
