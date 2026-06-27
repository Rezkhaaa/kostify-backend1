<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'booking_code', 'user_id', 'unit_id',
        'start_date', 'end_date', 'duration', 'total_price',
        'status', 'notes', 'rejection_reason', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    public function property(){ return $this->belongsTo(Property::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function approvedBy(){ return $this->belongsTo(User::class, 'approved_by'); }
    public function occupancy(){ return $this->hasOne(Occupancy::class); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }
}
