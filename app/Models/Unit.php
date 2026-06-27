<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'property_id', 'unit_code', 'name', 'type', 'description',
        'price', 'price_period', 'floor', 'area', 'capacity',
        'facilities', 'photo', 'status', 'address'
    ];

    protected $casts = [
        'facilities' => 'array',
        'price' => 'decimal:2',
    ];

    public function property(){ return $this->belongsTo(Property::class); }
    public function bookings(){ return $this->hasMany(Booking::class); }
    public function occupancies(){ return $this->hasMany(Occupancy::class); }
    public function billings(){ return $this->hasMany(Billing::class); }
    public function complaints(){ return $this->hasMany(Complaint::class); }
    public function maintenances(){ return $this->hasMany(Maintenance::class); }
    public function activeOccupancy(){ return $this->hasOne(Occupancy::class)->where('status', 'active'); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
