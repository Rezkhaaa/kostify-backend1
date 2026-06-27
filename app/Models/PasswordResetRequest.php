<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'email', 'phone', 'notes', 'status',
        'admin_notes', 'handled_by', 'handled_at',
    ];

    protected $casts = ['handled_at' => 'datetime'];

    public function property(){ return $this->belongsTo(Property::class); }
    public function handler(){ return $this->belongsTo(User::class, 'handled_by'); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where(function ($q) use ($admin) {
            $q->where('property_id', $admin->property_id)
              ->orWhereIn('email', User::where('property_id', $admin->property_id)->pluck('email'));
        });
    }
}
