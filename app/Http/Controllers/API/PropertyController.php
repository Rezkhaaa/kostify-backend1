<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user();

        // # Semua kos aktif tetap ditampilkan di mobile.
        // # Backend mengirim penanda can_book/gender_match agar UI bisa menjelaskan alasan jika tidak cocok.
        $query = Property::active()
            ->withCount([
                'units',
                'units as available_units_count' => fn ($q) => $q->where('status', 'available'),
                'units as occupied_units_count' => fn ($q) => $q->where('status', 'occupied'),
            ]);

        if ($request->filled('gender_type') && $request->gender_type !== 'all') {
            $query->where('gender_type', $request->gender_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        $properties = $query->orderBy('name')->get()->map(function (Property $property) use ($tenant) {
            $accessStatus = $tenant->statusForProperty((int) $property->id);
            $genderMatch = $property->allowsTenant($tenant);

            return $property->toArray() + [
                'gender_label' => $property->genderLabel(),
                'tenant_gender' => $tenant->gender,
                'tenant_gender_label' => $tenant->genderLabel(),
                'access_status' => $accessStatus,
                'gender_match' => $genderMatch,
                'can_book' => $accessStatus === 'active' && $genderMatch,
                'block_reason' => $accessStatus !== 'active'
                    ? 'Akses Anda untuk kos ini dinonaktifkan admin kos.'
                    : ($genderMatch ? null : 'Jenis kos ini tidak sesuai dengan kategori penghuni Anda.'),
            ];
        });

        return response()->json(['properties' => $properties]);
    }
}
