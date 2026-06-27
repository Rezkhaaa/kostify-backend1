<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'booking_id', 'user_id', 'unit_id',
        'start_date', 'end_date', 'status', 'notes'
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function property(){ return $this->belongsTo(Property::class); }
    public function booking(){ return $this->belongsTo(Booking::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function billings(){ return $this->hasMany(Billing::class); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }
}
