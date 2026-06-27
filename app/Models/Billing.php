<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'invoice_number', 'user_id', 'unit_id', 'occupancy_id',
        'title', 'amount', 'tax', 'total_amount',
        'billing_period_start', 'billing_period_end',
        'due_date', 'status', 'notes', 'created_by'
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function property(){ return $this->belongsTo(Property::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function createdBy(){ return $this->belongsTo(User::class, 'created_by'); }
    public function payments(){ return $this->hasMany(Payment::class); }
    public function payment(){ return $this->hasOne(Payment::class)->latestOfMany(); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'unpaid' && $this->due_date?->isPast();
    }
}
