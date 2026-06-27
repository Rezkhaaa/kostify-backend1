<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantRegistrationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'name', 'email', 'phone', 'address',
        'business_name', 'business_type', 'password', 'status',
        'admin_notes', 'approved_by', 'approved_at', 'rejected_at', 'created_user_id',
        'google_id', 'avatar', 'gender', 'requested_via',
    ];

    protected $hidden = ['password'];

    protected $casts = ['approved_at' => 'datetime', 'rejected_at' => 'datetime'];

    public function property(){ return $this->belongsTo(Property::class); }
    public function approver(){ return $this->belongsTo(User::class, 'approved_by'); }
    public function createdUser(){ return $this->belongsTo(User::class, 'created_user_id'); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where(function ($q) use ($admin) {
            $q->where('property_id', $admin->property_id)->orWhereNull('property_id');
        });
    }
}
