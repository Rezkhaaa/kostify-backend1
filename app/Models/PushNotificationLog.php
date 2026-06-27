<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'property_id', 'onesignal_notification_id', 'title', 'message',
        'data', 'status', 'response', 'error_message', 'sent_at',
        'tenant_read_at', 'admin_read_at', 'super_admin_read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'response' => 'array',
        'sent_at' => 'datetime',
        'tenant_read_at' => 'datetime',
        'admin_read_at' => 'datetime',
        'super_admin_read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function cleanupExpired(): void
    {
        static::where('created_at', '<', now()->subHours(24))->delete();
    }

    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subHours(24));
    }

    public function scopeUnreadForTenant($query, User $user)
    {
        return $query->where('user_id', $user->id)
            ->whereNull('tenant_read_at')
            ->recent();
    }

    public function scopeUnreadForAdmin($query, User $admin)
    {
        $query = $query->visibleTo($admin)->recent();

        if ($admin->isSuperAdmin()) {
            return $query->whereNull('super_admin_read_at');
        }

        return $query->whereNull('admin_read_at');
    }

    public static function markTenantRead(User $user): int
    {
        return static::unreadForTenant($user)->update(['tenant_read_at' => now()]);
    }

    public static function markAdminRead(User $admin): int
    {
        $query = static::unreadForAdmin($admin);

        return $query->update([
            $admin->isSuperAdmin() ? 'super_admin_read_at' : 'admin_read_at' => now(),
        ]);
    }


    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) {
            return $query->whereRaw('1 = 0');
        }

        if ($admin->isSuperAdmin()) {
            return $query;
        }

        return $query->where('property_id', $admin->property_id);
    }
}
