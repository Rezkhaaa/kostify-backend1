<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'bank_name',
        'account_number',
        'account_name',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function forProperty(?int $propertyId = null): self
    {
        $query = static::query()->where('is_active', true);

        $setting = null;
        if ($propertyId) {
            $setting = (clone $query)->where('property_id', $propertyId)->first();
        }

        $setting ??= (clone $query)->whereNull('property_id')->first();

        if ($setting) {
            return $setting;
        }

        return new self([
            'bank_name' => config('services.bank_transfer.bank_name', 'BCA'),
            'account_number' => config('services.bank_transfer.account_number', '1234567890'),
            'account_name' => config('services.bank_transfer.account_name', 'Kostify Residence'),
            'instructions' => config('services.bank_transfer.notes', 'Transfer sesuai nominal tagihan, lalu upload bukti pembayaran.'),
            'is_active' => true,
        ]);
    }

    public function toMobileArray(): array
    {
        return [
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'notes' => $this->instructions,
        ];
    }
}
