<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'property_id', 'name', 'email', 'password', 'google_id', 'role', 'gender',
        'phone', 'address', 'photo', 'avatar', 'onesignal_external_id',
        'onesignal_subscription_id', 'onesignal_enabled', 'onesignal_last_synced_at',
        'id_card_number', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'onesignal_enabled' => 'boolean',
        'onesignal_last_synced_at' => 'datetime',
    ];

    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isPropertyAdmin(): bool { return in_array($this->role, ['property_admin', 'admin'], true); }
    public function isAdmin(): bool { return $this->isSuperAdmin() || $this->isPropertyAdmin(); }
    public function isTenant(): bool { return $this->role === 'tenant'; }
    public function canManageAllProperties(): bool { return $this->isSuperAdmin(); }

    // # Label dipakai di Blade/API supaya orang awam mudah membaca nilai database.
    public function genderLabel(): string
    {
        return match ($this->gender) {
            'putra' => 'Penghuni Putra',
            'putri' => 'Penghuni Putri',
            default => 'Kategori penghuni belum diisi',
        };
    }

    public function property(){ return $this->belongsTo(Property::class); }
    public function propertyStatuses(){ return $this->hasMany(TenantPropertyStatus::class); }

    /**
     * # Status akses tenant untuk satu kos.
     * # Jika belum ada baris status, default-nya aktif agar tenant baru tetap bisa memilih kos.
     */
    public function statusForProperty(?int $propertyId): string
    {
        if (! $propertyId) return $this->status ?: 'active';
        $row = $this->propertyStatuses()->where('property_id', $propertyId)->first();
        return $row?->status ?? 'active';
    }

    public function isActiveForProperty(?int $propertyId): bool
    {
        if (! $this->isTenant()) return false;
        return $this->statusForProperty($propertyId) === 'active';
    }

    public function inactivePropertyIds(): array
    {
        if (! $this->isTenant()) return [];
        return $this->propertyStatuses()
            ->where('status', 'inactive')
            ->pluck('property_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }

    public function bookings(){ return $this->hasMany(Booking::class); }
    public function billings(){ return $this->hasMany(Billing::class); }
    public function complaints(){ return $this->hasMany(Complaint::class); }
    public function maintenances(){ return $this->hasMany(Maintenance::class); }
    public function activities(){ return $this->hasMany(ActivityHistory::class); }
    public function activeOccupancy(){ return $this->hasOne(Occupancy::class)->where('status', 'active'); }
}
