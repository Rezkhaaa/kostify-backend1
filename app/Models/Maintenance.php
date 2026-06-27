<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'maintenance_code', 'user_id', 'unit_id',
        'title', 'description', 'category', 'priority',
        'status', 'admin_notes', 'scheduled_date', 'completed_date',
        'cost', 'cost_payer', 'handled_by', 'photo'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
    ];

    protected $appends = ['photo_url', 'cost_payer_label'];

    public function property(){ return $this->belongsTo(Property::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function handledBy(){ return $this->belongsTo(User::class, 'handled_by'); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }



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

    public function getCostPayerLabelAttribute(): string
    {
        return match ($this->cost_payer) {
            'pengelola' => 'Pengelola',
            'tenant' => 'Penghuni',
            default => 'Belum Ditentukan',
        };
    }
}
