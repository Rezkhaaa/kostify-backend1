<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Models\Property;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function edit()
    {
        $admin = auth()->user();
        $properties = $admin->isSuperAdmin()
            ? Property::orderBy('name')->get()
            : collect([$admin->property])->filter();

        $settings = PaymentSetting::with('property')
            ->when(! $admin->isSuperAdmin(), fn ($q) => $q->where('property_id', $admin->property_id))
            ->orderByRaw('property_id is null desc')
            ->orderBy('property_id')
            ->get();

        $defaultSetting = PaymentSetting::forProperty($admin->isSuperAdmin() ? null : (int) $admin->property_id);

        return view('admin.payment-settings.edit', compact('properties', 'settings', 'defaultSetting'));
    }

    public function update(Request $request)
    {
        $admin = auth()->user();

        $rules = [
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'instructions' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];

        if ($admin->isSuperAdmin()) {
            $rules['property_id'] = 'nullable|exists:properties,id';
        }

        $data = $request->validate($rules, [
            'bank_name.required' => 'Nama bank wajib diisi.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_name.required' => 'Atas nama rekening wajib diisi.',
        ]);

        $propertyId = $admin->isSuperAdmin()
            ? ($data['property_id'] ?? null)
            : $admin->property_id;

        PaymentSetting::updateOrCreate(
            ['property_id' => $propertyId],
            [
                'bank_name' => $data['bank_name'],
                'account_number' => $data['account_number'],
                'account_name' => $data['account_name'],
                'instructions' => $data['instructions'] ?? 'Transfer sesuai nominal tagihan, lalu upload bukti pembayaran.',
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        return back()->with('success', 'Rekening pembayaran berhasil disimpan dan akan tampil di aplikasi mobile.');
    }
}
