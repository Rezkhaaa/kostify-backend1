<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPropertyStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'property_id', 'status', 'disabled_by', 'disabled_at', 'reason',
    ];

    protected $casts = [
        'disabled_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function disabledBy()
    {
        return $this->belongsTo(User::class, 'disabled_by');
    }
}
