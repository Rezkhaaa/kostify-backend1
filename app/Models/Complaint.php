<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'complaint_code', 'user_id', 'unit_id',
        'title', 'description', 'category', 'priority',
        'status', 'admin_response', 'handled_by', 'resolved_at', 'photo'
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    protected $appends = ['photo_url'];

    public function property(){ return $this->belongsTo(Property::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function handledBy(){ return $this->belongsTo(User::class, 'handled_by'); }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        return Storage::disk('public')->url($this->photo);
    }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }
}
