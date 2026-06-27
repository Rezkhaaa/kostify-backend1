<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile.edit', ['adminUser' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $user->update($data);

        if ($user->isPropertyAdmin() && $user->property) {
            $propertyData = $request->validate([
                'property_name' => 'nullable|string|max:255',
                'property_phone' => 'nullable|string|max:30',
                'property_address' => 'nullable|string|max:1000',
                'property_gender_type' => 'nullable|in:putra,putri,campuran',
                'property_max_units' => 'nullable|integer|min:1',
            ]);

            $user->property->update([
                'name' => $propertyData['property_name'] ?: $user->property->name,
                'phone' => $propertyData['property_phone'] ?? $user->property->phone,
                'address' => $propertyData['property_address'] ?? $user->property->address,
                'gender_type' => $propertyData['property_gender_type'] ?? $user->property->gender_type,
                'max_units' => $propertyData['property_max_units'] ?? $user->property->max_units,
            ]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($data['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Password berhasil diganti.');
    }
}
