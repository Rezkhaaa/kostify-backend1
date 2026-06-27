<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'payment_code', 'order_id', 'billing_id', 'user_id',
        'amount', 'method', 'status', 'reference_number', 'notes',
        'sender_name', 'sender_bank', 'transfer_date', 'proof_image',
        'bank_name', 'bank_account_number', 'bank_account_name', 'admin_note',
        'snap_token', 'snap_redirect_url', 'gateway_response',
        'paid_at', 'confirmed_by', 'confirmed_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'proof_url',
        'status_label',
    ];

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_image ? Storage::disk('public')->url($this->proof_image) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'success' => 'Success',
            'failed' => 'Fail',
            default => 'Checking Admin',
        };
    }

    public function property(){ return $this->belongsTo(Property::class); }
    public function billing(){ return $this->belongsTo(Billing::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function confirmedBy(){ return $this->belongsTo(User::class, 'confirmed_by'); }

    public function scopeVisibleTo($query, ?User $admin)
    {
        if (! $admin) return $query->whereRaw('1 = 0');
        if ($admin->isSuperAdmin()) return $query;
        return $query->where('property_id', $admin->property_id);
    }
}
