<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRegistrationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_name', 'email', 'phone', 'property_name', 'property_address',
        'gender_type', 'room_count', 'password', 'status', 'admin_notes', 'approved_by',
        'approved_at', 'rejected_at', 'created_property_id', 'created_admin_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function createdProperty()
    {
        return $this->belongsTo(Property::class, 'created_property_id');
    }

    public function createdAdmin()
    {
        return $this->belongsTo(User::class, 'created_admin_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
