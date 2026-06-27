<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'owner_name', 'phone', 'address', 'gender_type',
        'status', 'package_name', 'max_units', 'notes',
    ];

    // # Aturan utama Kos Putra/Putri/Campuran.
    // # Campuran menerima semua kategori penghuni.
    public function allowsTenant(?User $tenant): bool
    {
        if ($this->gender_type === 'campuran') return true;
        return $tenant?->gender === $this->gender_type;
    }

    public function genderLabel(): string
    {
        return match ($this->gender_type) {
            'putra' => 'Kos Putra',
            'putri' => 'Kos Putri',
            default => 'Kos Campuran',
        };
    }

    public function admins(){ return $this->hasMany(User::class)->whereIn('role', ['property_admin', 'admin']); }
    public function tenants(){ return $this->hasMany(User::class)->where('role', 'tenant'); }
    public function tenantStatuses(){ return $this->hasMany(TenantPropertyStatus::class); }
    public function units(){ return $this->hasMany(Unit::class); }
    public function bookings(){ return $this->hasMany(Booking::class); }
    public function billings(){ return $this->hasMany(Billing::class); }
    public function complaints(){ return $this->hasMany(Complaint::class); }
    public function maintenances(){ return $this->hasMany(Maintenance::class); }
    public function scopeActive($query){ return $query->where('status', 'active'); }
}
