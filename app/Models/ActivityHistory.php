<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'user_id', 'action', 'module',
        'description', 'subject_id', 'subject_type', 'meta', 'ip_address'
    ];

    protected $casts = ['meta' => 'array'];

    public function property(){ return $this->belongsTo(Property::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function subject(){ return $this->morphTo(); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }
}
